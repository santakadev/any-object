<?php

namespace Santakadev\AnyObject\Generator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\MatchArm;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;
use ReflectionClass;
use Santakadev\AnyObject\Parser\GraphNode;
use Santakadev\AnyObject\Parser\Parser;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TClass;
use Santakadev\AnyObject\Types\TEnum;
use Santakadev\AnyObject\Types\TNull;
use Santakadev\AnyObject\Types\TScalar;
use Santakadev\AnyObject\Types\TUnion;

class StubGenerator
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function generate(string $class): string
    {
        $root = $this->parser->parseThroughConstructor($class);

        // TODO: Traverse the tree and alter the PHPParser nodes to generate the stubs

        $reflectionClass = new ReflectionClass($root->type->class);
        $name = $reflectionClass->getShortName();
        $classNamespace = $reflectionClass->getNamespaceName();
        $stubName = $name . 'Factory';
        $factoryNamespace = 'Santakadev\\AnyObject\\Tests\\Generator\\Generated';

        $factory = new BuilderFactory;
        $node = $factory->namespace($factoryNamespace)
            ->addStmt($factory->use('Faker\Factory'))
            ->addStmt($factory->use("$classNamespace\\$name"))
            ->addStmt($factory->class($stubName)
                ->makeFinal()

                /* For each constructor I need to:
                 * - add a param with its type
                 * - add an if statement to check if the param is provided
                 * - add the param to the constructor call
                 * - if some param is a class (or array of that class), I need to recurse to generate the stub factory for that class
                 * - for enums I'm not sure if I need to generate a stub factory
                 * - for the rest I need to generate a random value
                 */
                ->addStmt($factory->method('with')
                    ->makePublic()
                    ->setReturnType($name)
                    ->addParams(
                        array_map(
                            fn (string $argName, GraphNode $n) => $factory
                                ->param($argName)
                                ->setType($n->type->value . '|ValueNotProvided')
                                ->setDefault($factory->new(new Name('ValueNotProvided'))),
                            array_keys($root->adjacencyList),
                            array_values($root->adjacencyList)
                        )
                    )
                    ->addStmts(
                        array_map(
                            fn (string $argName, GraphNode $n) =>
                                new If_(new Instanceof_(new Variable($argName), new Name('ValueNotProvided')), [
                                    'stmts' => [
                                        new Expression(new Assign(new Variable('faker'), $factory->staticCall(new Name('Factory'), 'create'))),
                                        new Expression(new Assign(new Variable($argName), $this->fakerFactory($n, $factory)))
                                    ]
                                ]),
                            array_keys($root->adjacencyList),
                            array_values($root->adjacencyList)
                        )
                    )
                    ->addStmt(new Return_($factory->new($name, array_map(fn ($name) => new Variable($name), array_keys($root->adjacencyList))))) // TODO: Not too readable
                )

                // Build method is not dependant of the tree
                ->addStmt($factory->method('build')
                    ->setReturnType($name)
                    ->makePublic()
                    ->addStmt(new Return_(new MethodCall(new Variable('this'), 'with')))
                )
            )

            ->getNode();
        $stmts = [$node];
        $prettyPrinter = new Standard();
        $file = $prettyPrinter->prettyPrintFile($stmts) . "\n";

        if (!is_dir(__DIR__ . "/../../tests/Generator/Generated")) {
            mkdir(__DIR__ . "/../../tests/Generator/Generated");
        }

        file_put_contents(__DIR__ . "/../../tests/Generator/Generated/$stubName.php", $file);

        $this->generateAnyFile();
        $this->generateValueNotProvidedFile();

        return $file;
    }

    public function fakerFactory(GraphNode $node, BuilderFactory $factory)
    {
        return match (get_class($node->type)) {
            TClass::class => [],
            TUnion::class => [],
            TArray::class => [],
            TEnum::class => [],
            TNull::class => [],
            TScalar::class => match ($node->type) {
                TScalar::string =>  $factory->methodCall(new Variable('faker'), 'text'),
                TScalar::int =>  $factory->methodCall(new Variable('faker'), 'numberBetween', [new ConstFetch(new Name('PHP_INT_MIN')), new ConstFetch(new Name('PHP_INT_MAX'))]),
                TScalar::float => [],
                TScalar::bool => [],
            },
        };
    }

    public function generateAnyFile(): void
    {
        $factory = new BuilderFactory;
        $node = $factory->namespace('Santakadev\AnyObject\Tests\Generator\Generated')
            ->addStmt($factory->use('Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject'))
            ->addStmt($factory->use('Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringIntObject'))
            ->addStmt($factory->use('Santakadev\AnyObject\Tests\TestData\ScalarTypes\IntObject'))
            ->addStmt($factory->class('Any')
                ->makeFinal()

                ->addStmt($factory->method('of')
                    ->makePublic()
                    ->makeStatic()
                    ->setReturnType('StringObjectFactory|IntObjectFactory|StringIntObjectFactory')
                    ->addParam(
                        $factory
                            ->param('class')
                            ->setType('string')
                    )
                    ->addStmt(new Return_(new Match_(new Variable('class'), [
                        new MatchArm([new ClassConstFetch(new Name('StringObject'), 'class')], $factory->new(new Name('StringObjectFactory'))),
                        new MatchArm([new ClassConstFetch(new Name('IntObject'), 'class')], $factory->new(new Name('IntObjectFactory'))),
                        new MatchArm([new ClassConstFetch(new Name('StringIntObject'), 'class')], $factory->new(new Name('StringIntObjectFactory'))),
                    ])))
                )
            )
            ->getNode();
        $stmts = [$node];
        $prettyPrinter = new Standard();
        $any = $prettyPrinter->prettyPrintFile($stmts) . "\n";
        file_put_contents(__DIR__ . "/../../tests/Generator/Generated/Any.php", $any);
    }

    public function generateValueNotProvidedFile(): void
    {
        $factory = new BuilderFactory;
        $node = $factory->namespace('Santakadev\AnyObject\Tests\Generator\Generated')
            ->addStmt($factory->class('ValueNotProvided')
                ->makeFinal()
            )
            ->getNode();
        $stmts = [$node];
        $prettyPrinter = new Standard();
        $valueNotProvided = $prettyPrinter->prettyPrintFile($stmts) . "\n";
        file_put_contents(__DIR__ . "/../../tests/Generator/Generated/ValueNotProvided.php", $valueNotProvided);
    }
}
