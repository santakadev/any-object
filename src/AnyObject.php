<?php

declare(strict_types=1);

namespace Santakadev\AnyObject;

use ReflectionClass;
use Santakadev\AnyObject\Parser\GraphNode;
use Santakadev\AnyObject\Parser\Parser;
use Santakadev\AnyObject\RandomGenerator\DateTimeImmutableBetween;
use Santakadev\AnyObject\RandomGenerator\Faker\Boolean;
use Santakadev\AnyObject\RandomGenerator\Faker\DateTimeBetween;
use Santakadev\AnyObject\RandomGenerator\Faker\NumberBetween;
use Santakadev\AnyObject\RandomGenerator\Faker\RandomFloat;
use Santakadev\AnyObject\RandomGenerator\Faker\Text;
use Santakadev\AnyObject\RandomGenerator\RandomArray;
use Santakadev\AnyObject\RandomGenerator\RandomArraySpec;
use Santakadev\AnyObject\RandomGenerator\RandomSpecRegistry;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TClass;
use Santakadev\AnyObject\Types\TEnum;
use Santakadev\AnyObject\Types\TNull;
use Santakadev\AnyObject\Types\TScalar;
use Santakadev\AnyObject\Types\TUnion;

class AnyObject
{
    private readonly Parser $parser;
    private RandomSpecRegistry $specRegistry;

    public function __construct(private readonly bool $useConstructor = true)
    {
        $this->parser = new Parser();
        $this->specRegistry = new RandomSpecRegistry();
        $this->specRegistry->register(new DateTimeBetween());
        $this->specRegistry->register(new DateTimeImmutableBetween());
        $this->specRegistry->register(new NumberBetween(PHP_INT_MIN, PHP_INT_MAX));
        $this->specRegistry->register(new Text());
        $this->specRegistry->register(new RandomFloat());
        $this->specRegistry->register(new Boolean());
    }

    public function of(string $class, array $with = []): object
    {
        if ($this->useConstructor) {
            return $this->buildFromConstructor($class, $with);
        } else {
            return $this->buildFromProperties($class, $with);
        }
    }

    private function buildFromConstructor(string $class, array $with = []): object
    {
        $root = $this->parser->parseThroughConstructor($class);
        $classBuilder = [$this, 'buildRandomClassThroughConstructor'];
        return $this->build($root, $with, $classBuilder);
    }

    private function buildFromProperties(string $class, array $with = []): object
    {
        $root = $this->parser->parseThroughProperties($class);
        $classBuilder = [$this, 'buildRandomClassThroughProperties'];
        return $this->build($root, $with, $classBuilder);
    }

    private function build(GraphNode $node, array $with, callable $classBuilder, array $visited = [])
    {
        if ($node->userDefinedSpec) {
            return $node->userDefinedSpec->generate();
        }

        $builder = fn(GraphNode $node) => $this->build($node, $with, $classBuilder, $visited);

        return match (get_class($node->type)) {
            TClass::class => $classBuilder($node, $with, $visited),
            TUnion::class => $this->buildRandomUnion($node, $builder),
            TArray::class => $this->buildRandomArray($node, $builder),
            TEnum::class => $node->type->pickRandomCase(),
            TNull::class => null,
            TScalar::class => $this->specRegistry->get($node->type->value)->generate()
        };
    }

    private function buildRandomArray(GraphNode $arrayNode, callable $builder): array
    {
        return $this->defaultArraySpec()->generate($arrayNode, $builder);
    }

    private function defaultArraySpec(): RandomArraySpec
    {
        return new RandomArray(0, 50);
    }

    private function buildRandomUnion(GraphNode $node, callable $builder)
    {
        return $builder($node->pickRandomBranch());
    }

    public function buildRandomClassThroughConstructor(GraphNode $node, array $with, array $visited): object
    {
        if ($this->specRegistry->has($node->type->class)) {
            return $this->specRegistry->get($node->type->class)->generate();
        }

        $arguments = [];

        foreach ($node->adjacencyList as $paramName => $adj) {
            if (isset($with[$paramName])) { // TODO: this could lead to strange results, as with can modify nested classes properties
                $arguments[] = $with[$paramName];
                continue; // TODO: There's no test for this case
            }

            if ($adj->type instanceof TClass && isset($visited[$adj->type->class])) {
                $arguments[] = $visited[$adj->type->class];
                continue;
            }

            $classBuilder = [$this, 'buildRandomClassThroughConstructor'];
            $value = $this->build($adj, $with, $classBuilder, $visited);
            if ($value instanceof TClass) {
                $visited[$adj->type->class] = $value;
            }

            $arguments[] = $value; // TODO: Reuse built objects
        }

        return $node->type->build($arguments);
    }

    public function buildRandomClassThroughProperties(GraphNode $node, array $with, array $visited): object
    {
        if ($this->specRegistry->has($node->type->class)) {
            return $this->specRegistry->get($node->type->class)->generate();
        }

        $reflectionClass = new ReflectionClass($node->type->class);
        $instance = $reflectionClass->newInstanceWithoutConstructor();
        $visited[$node->type->class] = $instance;
        $values = [];
        foreach ($node->adjacencyList as $propertyName => $adj) {
            if (isset($with[$propertyName])) { // TODO: this could lead to strange results, as with can modify nested classes properties
                $values[$propertyName] = $with[$propertyName];
                continue;
            }

            if ($adj->type instanceof TClass && isset($visited[$adj->type->class])) {
                $values[$propertyName] = $visited[$adj->type->class];
                continue;
            }

            $classBuilder = [$this, 'buildRandomClassThroughProperties'];
            $value = $this->build($adj, $with, $classBuilder, $visited);
            if ($value instanceof TClass) {
                $visited[$adj->type->class] = $value;
            }

            $values[$propertyName] = $value; // TODO: Reuse built objects
        }

        foreach ($values as $propertyName => $value) {
            $reflectionProperty = $reflectionClass->getProperty($propertyName);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($instance, $value);
            $reflectionProperty->setAccessible(false);
        }

        return $instance;
    }
}
