<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

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
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
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
use Santakadev\AnyObject\RandomGenerator\RandomSpecRegistry;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TClass;
use Santakadev\AnyObject\Types\TEnum;
use Santakadev\AnyObject\Types\TNull;
use Santakadev\AnyObject\Types\TScalar;
use Santakadev\AnyObject\Types\TUnion;

class BuilderGenerator implements GeneratorInterface
{
    private readonly Parser $parser;
    private readonly RandomSpecRegistry $specRegistry;

    public function __construct()
    {
        $this->parser = new Parser();
        $this->specRegistry = RandomSpecRegistry::default();
    }

    // TODO: OutputResolver and NameResolver could be properties, so there would no need to pass them on each generate.
    public function generate(string $class, OutputResolver $outputResolver, ?NameResolver $nameResolver = null): void
    {
        if (!$nameResolver) {
            $nameResolver = new WrapNameResolver(prefix: 'Any', suffix: 'Builder');
        }

        $root = $this->parser->parseThroughConstructor($class);
        foreach (DFSIterator::walkClass($root) as $node) {
            $this->generateClass($node, $outputResolver, $nameResolver);
        }
    }

    private function generateClass(GraphNode $node, OutputResolver $outputResolver, NameResolver $nameResolver): void
    {
        $reflectionClass = new ReflectionClass($node->type->class);
        $name = $reflectionClass->getShortName();
        $classNamespace = $reflectionClass->getNamespaceName();
        $stubName = $nameResolver->resolve($name);
        $output = $outputResolver->resolve($node->type->class);

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

        // TODO: Support variadic named constructor
        // TODO: Support arrays
        $createStmts = [
            $this->initializeFaker($factory),
            ...array_merge(...array_map(
                fn(string $argName, GraphNode $n) => $this->buildRandomArgumentValueStatements($argName, $n, $factory, $outputResolver, $nameResolver),
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

        $constructorArgs = array_map(fn($name) => new PropertyFetch(new Variable('this'), $name), array_keys($node->adjacencyList));

        if ($node->type->isVariadic) {
            $lastKey = array_key_last($constructorArgs);
            $constructorArgs[$lastKey] = new Arg($constructorArgs[$lastKey], false, true);
        }

        if ($node->type->constructor === '__construct') {
            $newStmt = new Return_($factory->new($name, $constructorArgs));
        } else {
            $newStmt = new Return_($factory->staticCall($name, new Identifier($node->type->constructor), $constructorArgs));
        }

        $buildMethod = $factory->method('build')
            ->makePublic()
            ->setReturnType($name)
            ->addStmt($newStmt);

        $nodeBuilder = $factory->namespace($output->namespace)
            ->addStmt($factory->use('Faker\Factory'))
            ->addStmt($factory->use("$classNamespace\\$name"));

        $uses = $this->getUses($node, $output->namespace, $nameResolver, $outputResolver);

        $nodeBuilder->addStmts(
            array_map(fn (string $class) => $factory->use($class), array_keys($uses))
        );

        $nodeBuilder->addStmt(new Nop())
            ->addStmt($factory->class($stubName)
                ->makeFinal()
                ->addStmt($constructor)
                ->addStmt($create)
                ->addStmts($withMethods)
                ->addStmt($buildMethod)
            );
        $stmts = [$nodeBuilder->getNode()];
        $prettyPrinter = new Standard(['shortArraySyntax' => true]);
        $file = $prettyPrinter->prettyPrintFile($stmts) . "\n";


        if (!is_dir($output->path)) {
            mkdir($output->path);
        }

        file_put_contents($output->path . DIRECTORY_SEPARATOR . "$stubName.php", $file);
    }

    private function buildRandomArgumentValueStatements(string $argName, GraphNode $node, BuilderFactory $factory, OutputResolver $outputResolver, NameResolver $nameResolver): array
    {
        return match (get_class($node->type)) {
            TArray::class => $this->buildRandomArrayArgumentValueStatements($argName, $node, $factory, $outputResolver, $nameResolver),
            default => [
                new Expression(new Assign(new Variable($argName), $this->buildRandom($node, $factory, $nameResolver)))
            ]
        };
    }

    // TODO: review if is there any better alternative to passing $nameResolver down
    private function buildRandom(GraphNode $node, BuilderFactory $factory, NameResolver $nameResolver)
    {
        if ($node->userDefinedSpec) {
            return $node->userDefinedSpec->generateCode($factory);
        }

        return match (get_class($node->type)) {
            TClass::class => $this->buildRandomClass($factory, $node, $nameResolver),
            TUnion::class => $this->buildRandomUnion($node, $factory, $nameResolver),
            TArray::class => $this->buildRandomArray($node, $factory, $nameResolver),
            TEnum::class => $this->buildRandomEnum($node, $factory),
            TNull::class => new ConstFetch(new Name('null')),
            TScalar::class => $this->specRegistry->get($node->type->value)->generateCode($factory),
        };
    }

    private function classShortName(string $class): string
    {
        return (new ReflectionClass($class))->getShortName();
    }

    private function buildRandomUnion(GraphNode $node, BuilderFactory $factory, NameResolver $nameResolver): Match_
    {
        $types = array_map(fn ($type) => $this->typeFromGraphNode($type), $node->adjacencyList);

        $arrayRandFuncCall = new FuncCall(
            new Name('array_rand'),
            [
                new Arg(new Array_(array_map(fn($type) => new String_($type), $types)))
            ]
        );

        $matchArms = array_map(fn (GraphNode $node, int $index) => new MatchArm([new LNumber($index)], $this->buildRandom($node, $factory, $nameResolver)), $node->adjacencyList, array_keys($node->adjacencyList));

        return new Match_($arrayRandFuncCall, $matchArms);
    }

    private function buildRandomArray(GraphNode $node, BuilderFactory $factory, NameResolver $nameResolver)
    {
        if (count($node->adjacencyList) === 1) {
            return $this->buildRandom($node->adjacencyList[0], $factory, $nameResolver);
        } else {
            return $this->buildRandomUnion($node, $factory, $nameResolver);
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

    private function buildRandomClass(BuilderFactory $factory, GraphNode $node, NameResolver $nameResolver): Expr
    {
        if ($this->specRegistry->has($node->type->class)) {
            return $this->specRegistry->get($node->type->class)->generateCode($factory);
        }

        return $factory->methodCall($factory->staticCall($nameResolver->resolve($this->classShortName($node->type->class)), 'create'), 'build');
    }

    private function initializeFaker(BuilderFactory $factory): Expression
    {
        return new Expression(new Assign(new Variable('faker'), $factory->staticCall(new Name('Factory'), 'create')));
    }

    private function buildRandomArrayArgumentValueStatements(string $argName, GraphNode $node, BuilderFactory $factory, OutputResolver $outputResolver, NameResolver $nameResolver)
    {
        // TODO: 2 responsibilities here: children classes generation and build random array statements
        foreach ($node->adjacencyList as $child) {
            if ($child->type instanceof TClass) {
                $this->generate($child->type->class, $outputResolver, $nameResolver);
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
                        new Expression(new Assign(new ArrayDimFetch(new Variable($argName)), $this->buildRandom($node, $factory, $nameResolver)))
                    ]
                ]
            )
        ];
    }

    // TODO: duplicated code
    private function getUses(GraphNode $node, string $rootOutputNamespace, NameResolver $nameResolver, OutputResolver $outputResolver, int $depth = 0): array
    {
        // TODO: This could lead to a unused import then there is TClass -> TClass -> TClass hierarchy
        if ($depth > 2) {
            return [];
        }

        // ignore root node
        if ($depth > 0) {
            if ($node->type instanceof TClass) {
                return $this->getUsesOfClassName($node->type->class, $nameResolver, $outputResolver, $rootOutputNamespace);
            }
        }

        if ($node->type instanceof TEnum) {
            return $this->getUsesOfClassName($this->enumName($node->type), $nameResolver, $outputResolver, $rootOutputNamespace);
        }

        // only traverse TClass and TUnion
        if (!in_array(get_class($node->type), [TClass::class, TUnion::class])) {
            return [];
        }

        $uses = [];

        foreach ($node->adjacencyList as $child) {
            $uses[] = $this->getUses($child, $rootOutputNamespace, $nameResolver, $outputResolver, $depth + 1);
        }

        return array_merge(...$uses);
    }

    private function getUsesOfClassName(string $className, NameResolver $nameResolver, OutputResolver $outputResolver, string $rootOutputNamespace): array
    {
        $uses = [$className => true];

        if (enum_exists($className)) {
            return $uses;
        }

        $reflectionClass = new ReflectionClass($className);

        $name = $nameResolver->resolve($reflectionClass->getShortName());
        $output = $outputResolver->resolve($className);

        if ($output->namespace !== $rootOutputNamespace) {
            $uses[$output->namespace . '\\' . $name] = true;
        }

        return $uses;
    }
}
