<?php

namespace Santakadev\AnyObject\Generator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
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
use Santakadev\AnyObject\Parser\Parser;

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
                    ->addParam(
                        $factory
                            ->param('value')
                            ->setType('string|ValueNotProvided')
                            ->setDefault($factory->new(new Name('ValueNotProvided')))
                    )
                    ->addStmt(new If_(new Instanceof_(new Variable('value'), new Name('ValueNotProvided')), [
                        'stmts' => [
                            new Expression(new Assign(new Variable('faker'), $factory->staticCall(new Name('Factory'), 'create'))),
                            new Expression(new Assign(new Variable('value'), $factory->methodCall(new Variable('faker'), 'text' )))
                        ]
                    ]))
                    ->addStmt(new Return_($factory->new($name, [new Variable('value')])))
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

    public function generateAnyFile(): void
    {
        $factory = new BuilderFactory;
        $node = $factory->namespace('Santakadev\AnyObject\Tests\Generator\Generated')
            ->addStmt($factory->use('Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject'))
            ->addStmt($factory->class('Any')
                ->makeFinal()

                ->addStmt($factory->method('of')
                    ->makePublic()
                    ->makeStatic()
                    ->setReturnType('StringObjectFactory')
                    ->addParam(
                        $factory
                            ->param('class')
                            ->setType('string')
                    )
                    ->addStmt(new Return_(new Match_(new Variable('class'), [
                        new MatchArm([new ClassConstFetch(new Name('StringObject'), 'class')], $factory->new(new Name('StringObjectFactory')))
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
