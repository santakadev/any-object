<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

use DateTime;
use DateTimeImmutable;
use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\MatchArm;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;
use ReflectionClass;
use Santakadev\AnyObject\Parser\GraphNode;
use Santakadev\AnyObject\Parser\Parser;
use Santakadev\AnyObject\RandomGenerator\Boolean;
use Santakadev\AnyObject\RandomGenerator\NumberBetween;
use Santakadev\AnyObject\RandomGenerator\RandomBoolSpec;
use Santakadev\AnyObject\RandomGenerator\RandomFloat;
use Santakadev\AnyObject\RandomGenerator\RandomFloatSpec;
use Santakadev\AnyObject\RandomGenerator\RandomIntSpec;
use Santakadev\AnyObject\RandomGenerator\RandomStringSpec;
use Santakadev\AnyObject\RandomGenerator\Text;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TClass;
use Santakadev\AnyObject\Types\TEnum;
use Santakadev\AnyObject\Types\TNull;
use Santakadev\AnyObject\Types\TScalar;
use Santakadev\AnyObject\Types\TUnion;

class BuilderGenerator
{
    private readonly Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    // TODO: Read from psr-4 from package.json to build the namespace based on the $outputDir
    public function generate(string $class, string $outputDir, $outputNamespace): void
    {
        $root = $this->parser->parseThroughConstructor($class);
        foreach (DFSIterator::walkClass($root) as $node) {
            $this->generateClass($node, $outputDir, $outputNamespace);
        }
    }

    public function generateClass(GraphNode $node, string $outputDir, string $outputNamespace): void
    {
        $reflectionClass = new ReflectionClass($node->type->class);
        $name = $reflectionClass->getShortName();
        $classNamespace = $reflectionClass->getNamespaceName();
        $stubName = 'Any' . $name . 'Builder';

        $factory = new BuilderFactory;

        $constructor = $factory->method('__construct')
            ->makePrivate()
            ->addParams(
                array_map(
                    fn (string $argName, GraphNode $n) => $factory
                        ->param($argName)
                        ->setType($this->typeFromGraphNode($n))
                        ->makePrivate(),
                    array_keys($node->adjacencyList),
                    array_values($node->adjacencyList),
                )
            );

        // TODO: Support arrays
        $createStmts = [
            $this->initializeFaker($factory),
            ...array_merge(...array_map(
                fn(string $argName, GraphNode $n) => $this->buildRandomArgumentValueStatements($argName, $n, $factory, $outputDir, $outputNamespace),
                array_keys($node->adjacencyList),
                array_values($node->adjacencyList)
            )),
            new Return_($factory->new('self', array_map(fn($name) => new Variable($name), array_keys($node->adjacencyList))))
        ];
        $create = $factory->method('create')
            ->makePublic()
            ->makeStatic()
            ->setReturnType('self')
            ->addStmts($createStmts);

        $withMethods = array_map(
            fn (string $argName, GraphNode $node) => $factory->method('with' . ucfirst($argName))
                ->makePublic()
                ->addParam(
                    $factory
                        ->param($argName)
                        ->setType($this->typeFromGraphNode($node))
                )
                ->setReturnType('self')
                ->addStmts([
                    new Expression(new Assign(new PropertyFetch(new Variable('this'), $argName), new Variable($argName))),
                    new Return_(new Variable('this'))
                ]),
            array_keys($node->adjacencyList),
            array_values($node->adjacencyList)
        );

        // TODO: Support named constructors
        // TODO: Support variadic named constructor
        $constructorArgs = array_map(fn($name) => new PropertyFetch(new Variable('this'), $name), array_keys($node->adjacencyList));

        if ($node->type->isVariadic) {
            $lastKey = array_key_last($constructorArgs);
            $constructorArgs[$lastKey] = new Arg($constructorArgs[$lastKey], false, true);
        }

        $buildMethod = $factory->method('build')
            ->makePublic()
            ->setReturnType($name)
            ->addStmt(
                new Return_($factory->new($name, $constructorArgs))
            );

        $nodeBuilder = $factory->namespace($outputNamespace)
            ->addStmt($factory->use('Faker\Factory'))
            ->addStmt($factory->use("$classNamespace\\$name"));

        foreach ($node->adjacencyList as $child) {
            if ($child->type instanceof TClass) {
                $nodeBuilder->addStmt($factory->use($child->type->class));
            }
            if ($child->type instanceof TUnion) {
                foreach ($child->type->types as $type) {
                    if ($type instanceof TClass) {
                        $nodeBuilder->addStmt($factory->use($type->class));
                    }
                    if ($type instanceof TEnum) {
                        $nodeBuilder->addStmt($factory->use($this->enumName($type)));
                    }
                }
            }
            if ($child->type instanceof TEnum) {
                $nodeBuilder->addStmt($factory->use($this->enumName($child->type)));
            }
        }

        $nodeBuilder->addStmt(new Nop())
            ->addStmt($factory->class($stubName)
                ->makeFinal()
                ->addStmt($constructor)
                ->addStmt($create)
                ->addStmts($withMethods)
                ->addStmt($buildMethod)
            );
        $node = $nodeBuilder->getNode();
        $stmts = [$node];
        $prettyPrinter = new Standard(['shortArraySyntax' => true]);
        $file = $prettyPrinter->prettyPrintFile($stmts) . "\n";

        if (!is_dir($outputDir)) {
            mkdir($outputDir);
        }

        file_put_contents($outputDir . DIRECTORY_SEPARATOR . "$stubName.php", $file);
    }

    private function buildRandomArgumentValueStatements(string $argName, GraphNode $node, BuilderFactory $factory, string $outputDir, string $outputNamespace): array
    {
        return match (get_class($node->type)) {
            TArray::class => $this->buildRandomArrayArgumentValueStatements($argName, $node, $factory, $outputDir, $outputNamespace),
            default => [
                new Expression(new Assign(new Variable($argName), $this->buildRandom($node, $factory)))
            ]
        };
    }

    private function buildRandom(GraphNode $node, BuilderFactory $factory)
    {
        return match (get_class($node->type)) {
            TClass::class => $this->buildRandomClass($factory, $node),
            TUnion::class => $this->buildRandomUnion($node, $factory),
            TArray::class => $this->buildRandomArray($node, $factory),
            TEnum::class => $this->buildRandomEnum($node, $factory),
            TNull::class => new ConstFetch(new Name('null')),
            TScalar::class => match ($node->type) {
                TScalar::string => $this->buildRandomString($node->userDefinedSpec, $factory),
                TScalar::int => $this->buildRandomInt($node->userDefinedSpec, $factory),
                TScalar::float => $this->buildRandomFloat($node->userDefinedSpec, $factory),
                TScalar::bool => $this->buildRandomBool($node->userDefinedSpec, $factory),
            },
        };
    }

    private function buildRandomInt(?RandomIntSpec $userDefinedSpec, BuilderFactory $factory): Expr
    {
        $spec = $userDefinedSpec ?? $this->defaultIntSpec();

        return $spec->generateCode($factory);
    }

    // TODO: Duplicated code
    private function defaultIntSpec(): RandomIntSpec
    {
        return new NumberBetween(PHP_INT_MIN, PHP_INT_MAX);
    }

    private function buildRandomString(?RandomStringSpec $userDefinedSpec, BuilderFactory $factory): Expr
    {
        $spec = $userDefinedSpec ?? $this->defaultStringSpec();

        return $spec->generateCode($factory);
    }

    // TODO: Duplicated code
    private function defaultStringSpec(): RandomStringSpec
    {
        return new Text();
    }

    private function buildRandomFloat(?RandomFloatSpec $userDefinedSpec, BuilderFactory $factory): Expr
    {
        $spec = $userDefinedSpec ?? $this->defaultFloatSpec();

        return $spec->generateCode($factory);
    }

    // TODO: Duplicated code
    private function defaultFloatSpec(): RandomFloatSpec
    {
        return new RandomFloat();
    }

    private function buildRandomBool(?RandomBoolSpec $userDefinedSpec, $factory): Expr
    {
        $spec = $userDefinedSpec ?? $this->defaultBoolSpec();

        return $spec->generateCode($factory);
    }

    // TODO: Duplicated code
    private function defaultBoolSpec(): RandomBoolSpec
    {
        return new Boolean();
    }

    private function classShortName(string $class): string
    {
        return (new ReflectionClass($class))->getShortName();
    }

    private function buildRandomUnion(GraphNode $node, BuilderFactory $factory): Match_
    {
        $types = array_map(fn ($type) => $this->typeFromGraphNode($type), $node->adjacencyList);

        $arrayRandFuncCall = new FuncCall(
            new Name('array_rand'),
            [
                new Arg(new Array_(array_map(fn($type) => new String_($type), $types)))
            ]
        );

        $matchArms = array_map(fn (GraphNode $node, int $index) => new MatchArm([new LNumber($index)], $this->buildRandom($node, $factory)), $node->adjacencyList, array_keys($node->adjacencyList));

        return new Match_($arrayRandFuncCall, $matchArms);
    }

    private function buildRandomArray(GraphNode $node, BuilderFactory $factory)
    {
        if (count($node->adjacencyList) === 1) {
            return $this->buildRandom($node->adjacencyList[0], $factory);
        } else {
            return $this->buildRandomUnion($node, $factory);
        }
    }

    private function buildRandomEnum(GraphNode $node, BuilderFactory $factory): ArrayDimFetch
    {
        $enumTypeCasesStaticCall = $factory->staticCall(new Name($this->enumShortName($node->type)), 'cases');

        $arrayRandFuncCall = $factory->funcCall(new Name('array_rand'), [
            new Arg($enumTypeCasesStaticCall)
        ]);

        return new ArrayDimFetch($enumTypeCasesStaticCall, $arrayRandFuncCall);
    }

    private function typeFromGraphNode(GraphNode $node): string
    {
        return match (get_class($node->type)) {
            TClass::class => $this->classShortName($node->type->class),
            TUnion::class => rtrim(array_reduce($node->type->types, fn($acc, $type) => $acc . $this->typeFromGraphNode(new GraphNode($type)) . '|', ''), '|'),
            TArray::class => 'array', // TODO: should I add the type of the array in the PHPDoc?
            TEnum::class => $this->enumShortName($node->type),
            TNull::class => 'null',
            TScalar::class => $node->type->value,
        };
    }

    private function enumShortName(TEnum $enumType): string
    {
        return $this->classShortName($this->enumName($enumType)); // TODO: this is risky if the enum has no cases. I think I should add a property with the enum type
    }

    private function enumName(TEnum $enumType): string
    {
        return get_class($enumType->values[0]);
    }

    private function buildRandomClass(BuilderFactory $factory, GraphNode $node): MethodCall|New_
    {
        if ($node->type->class === DateTime::class) {
            return $factory->new('DateTime');
        } elseif ($node->type->class === DateTimeImmutable::class) {
            return $factory->new('DateTimeImmutable');
        }

        return $factory->methodCall($factory->staticCall('Any' . $this->classShortName($node->type->class) . 'Builder', 'create'), 'build');
    }

    private function initializeFaker(BuilderFactory $factory): Expression
    {
        return new Expression(new Assign(new Variable('faker'), $factory->staticCall(new Name('Factory'), 'create')));
    }

    private function buildRandomArrayArgumentValueStatements(string $argName, GraphNode $node, BuilderFactory $factory, string $outputDir, string $outputNamespace)
    {
        // TODO: 2 responsibilities here: children classes generation and build random array statements
        foreach ($node->adjacencyList as $child) {
            if ($child->type instanceof TClass) {
                $this->generate($child->type->class, $outputDir, $outputNamespace); // TODO: here I'm relying in the default outputDir :/
            }
        }

        return [
            new Expression(new Assign(new Variable('minElements'), new LNumber(0))),
            new Expression(new Assign(new Variable('maxElements'), new LNumber(50))),
            new Expression(new Assign(new Variable('elementsCount'), $factory->methodCall(new Variable('faker'), 'numberBetween', [new Variable('minElements'), new Variable('maxElements')]))),
            new Expression(new Assign(new Variable($argName), new Array_())),
            new For_(
                [
                    'init' => [new Assign(new Variable('i'), new LNumber(0))],
                    'cond' => [new Smaller(new Variable('i'), new Variable('elementsCount'))],
                    'loop' => [new PostInc(new Variable('i'))],
                    'stmts' => [
                        new Expression(new Assign(new ArrayDimFetch(new Variable($argName)), $this->buildRandom($node, $factory)))
                    ]
                ]
            )
        ];
    }
}
