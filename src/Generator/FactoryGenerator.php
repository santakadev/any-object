<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

use DateTime;
use DateTimeImmutable;
use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\MatchArm;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;
use ReflectionClass;
use Santakadev\AnyObject\Parser\GraphNode;
use Santakadev\AnyObject\Parser\Parser;
use Santakadev\AnyObject\RandomGenerator\Boolean;
use Santakadev\AnyObject\RandomGenerator\NumberBetween;
use Santakadev\AnyObject\RandomGenerator\RandomDateTime;
use Santakadev\AnyObject\RandomGenerator\RandomDateTimeImmutable;
use Santakadev\AnyObject\RandomGenerator\RandomFloat;
use Santakadev\AnyObject\RandomGenerator\RandomSpecRegistry;
use Santakadev\AnyObject\RandomGenerator\Text;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TClass;
use Santakadev\AnyObject\Types\TEnum;
use Santakadev\AnyObject\Types\TNull;
use Santakadev\AnyObject\Types\TScalar;
use Santakadev\AnyObject\Types\TUnion;

class FactoryGenerator
{
    private readonly Parser $parser;
    private readonly RandomSpecRegistry $specRegistry;

    public function __construct()
    {
        $this->parser = new Parser();
        $this->specRegistry = new RandomSpecRegistry();
        $this->specRegistry->register(new RandomDateTime());
        $this->specRegistry->register(new RandomDateTimeImmutable());
        $this->specRegistry->register(new NumberBetween(PHP_INT_MIN, PHP_INT_MAX));
        $this->specRegistry->register(new Text());
        $this->specRegistry->register(new RandomFloat());
        $this->specRegistry->register(new Boolean());
    }

    // TODO: Read from psr-4 from package.json to build the namespace based on the $outputDir
    public function generate(string $class, string $outputDir, string $outputNamespace): void
    {
        $root = $this->parser->parseThroughConstructor($class);
        foreach (DFSIterator::walkClass($root) as $node) {
            $this->generateClass($node, $outputDir, $outputNamespace);
        }
    }

    private function generateClass(GraphNode $node, string $outputDir, string $outputNamespace): void
    {
        $reflectionClass = new ReflectionClass($node->type->class);
        $name = $reflectionClass->getShortName();
        $classNamespace = $reflectionClass->getNamespaceName();
        $stubName = 'Any' . $name;

        $factory = new BuilderFactory;

        $withMethod = $factory->method('with')
            ->makePublic()
            ->makeStatic()
            ->setReturnType($name)
            ->addParams(
                array_map(
                    fn(string $argName, GraphNode $n) => $factory
                        ->param($argName)
                        ->setType($this->typeFromGraphNode($n) . '|None')
                        ->setDefault($factory->new(new Name('None'))),
                    array_keys($node->adjacencyList),
                    array_values($node->adjacencyList)
                )
            )
            ->addStmts(
                array_map(
                    fn(string $argName, GraphNode $n) => new If_(new Instanceof_(new Variable($argName), new Name('None')), [
                        'stmts' => $this->buildRandomArgumentValueStatements($argName, $n, $factory, $outputDir, $outputNamespace)
                    ]),
                    array_keys($node->adjacencyList),
                    array_values($node->adjacencyList)
                )
            );

        $constructorArgs = array_map(fn($name) => new Variable($name), array_keys($node->adjacencyList));

        if ($node->type->isVariadic) {
            $lastKey = array_key_last($constructorArgs);
            $constructorArgs[$lastKey] = new Arg($constructorArgs[$lastKey], false, true);
        }

        if ($node->type->constructor === '__construct') {
            $withMethod->addStmt(new Return_($factory->new($name, $constructorArgs)));
        } else {
            $withMethod->addStmt(new Return_($factory->staticCall($name, new Identifier($node->type->constructor), $constructorArgs)));
        }

        $buildMethod = $factory->method('build')
            ->setReturnType($name)
            ->makePublic()
            ->makeStatic()
            ->addStmt(new Return_($factory->staticCall(new Name('self'), 'with')));

        $nodeBuilder = $factory->namespace($outputNamespace)
            ->addStmt($factory->use('Faker\Factory'))
            ->addStmt($factory->use("$classNamespace\\$name"));

        $uses = [];
        foreach ($node->adjacencyList as $child) {
            if ($child->type instanceof TClass) {
                $uses[$child->type->class] = true;
            }
            if ($child->type instanceof TUnion) {
                foreach ($child->type->types as $type) {
                    if ($type instanceof TClass) {
                        $uses[$type->class] = true;
                    }
                    if ($type instanceof TEnum) {
                        $uses[$this->enumName($type)] = true;
                    }
                }
            }
            if ($child->type instanceof TEnum) {
                $uses[$this->enumName($child->type)] = true;
            }
        }

        $nodeBuilder->addStmts(
            array_map(fn (string $class) => $factory->use($class), array_keys($uses))
        );

        $nodeBuilder->addStmt(new Nop())
            ->addStmt($factory->class($stubName)
                ->makeFinal()
                ->addStmt($withMethod)
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

        $this->generateNoneFile($outputDir, $outputNamespace);
    }

    private function buildRandomArgumentValueStatements(string $argName, GraphNode $node, BuilderFactory $factory, string $outputDir, string $outputNamespace): array
    {
        return match (get_class($node->type)) {
            TArray::class => $this->buildRandomArrayArgumentValueStatements($argName, $node, $factory, $outputDir, $outputNamespace),
            default => [
                $this->initializeFaker($factory),
                new Expression(new Assign(new Variable($argName), $this->buildRandom($node, $factory)))
            ]
        };
    }

    private function buildRandom(GraphNode $node, BuilderFactory $factory)
    {
        if ($node->userDefinedSpec) {
            return $node->userDefinedSpec->generateCode($factory);
        }

        return match (get_class($node->type)) {
            TClass::class => $this->buildRandomClass($factory, $node),
            TUnion::class => $this->buildRandomUnion($node, $factory),
            TArray::class => $this->buildRandomArray($node, $factory),
            TEnum::class => $this->buildRandomEnum($node, $factory),
            TNull::class => new ConstFetch(new Name('null')),
            TScalar::class => $this->specRegistry->get($node->type->value)->generateCode($factory),
        };
    }

    private function classShortName(string $class): string
    {
        return (new ReflectionClass($class))->getShortName();
    }

    private function generateNoneFile(string $outputDir, string $outputNamespace): void
    {
        $factory = new BuilderFactory;
        $node = $factory->namespace($outputNamespace)
            ->addStmt($factory->class('None')
                ->makeFinal()
            )
            ->getNode();
        $stmts = [$node];
        $prettyPrinter = new Standard();
        $None = $prettyPrinter->prettyPrintFile($stmts) . "\n";
        file_put_contents($outputDir . DIRECTORY_SEPARATOR . "None.php", $None);
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

    private function buildRandomClass(BuilderFactory $factory, GraphNode $node): StaticCall|New_
    {
        if ($node->type->class === DateTime::class) {
            return $factory->new('DateTime');
        } elseif ($node->type->class === DateTimeImmutable::class) {
            return $factory->new('DateTimeImmutable');
        }

        return $factory->staticCall('Any' . $this->classShortName($node->type->class), 'build');
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
            $this->initializeFaker($factory),
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
