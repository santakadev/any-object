<?php

namespace Santakadev\AnyObject;

use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
use Santakadev\AnyObject\Parser\GraphNode;
use Santakadev\AnyObject\Parser\Parser;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TClass;
use Santakadev\AnyObject\Types\TEnum;
use Santakadev\AnyObject\Types\TNull;
use Santakadev\AnyObject\Types\TScalar;
use Santakadev\AnyObject\Types\TUnion;
use SplStack;

class AnyObject
{
    private readonly Generator $faker;
    private readonly Parser $parser;

    public function __construct(private readonly bool $useConstructor = true)
    {
        $this->faker = Factory::create();
        $this->parser = new Parser();
    }

    public function of(string $class, array $with = []): object
    {
        // TODO: think the best way of handling circular references
        if ($this->useConstructor) {
            return $this->buildFromConstructor($class, $with);
        } else {
            return $this->buildFromProperties($class, $with);
        }
    }

    private function buildFromConstructor(string $class, array $with = []): object
    {
        $root = $this->parser->parseThroughConstructor($class);
        $classBuilder = $this->getRandomClassBuilderThroughConstructor();
        return $this->buildRecursively($root, $with, $classBuilder);
    }

    private function buildFromProperties(string $class, array $with = []): object
    {
        $root = $this->parser->parseThroughProperties($class);
        $classBuilder = $this->getRandomClassBuilderThroughProperties();
        return $this->buildRecursively($root, $with, $classBuilder);
    }

    private function buildRecursively(GraphNode $node, array $with, callable $classBuilder, array $visited = [])
    {
        return match (get_class($node->type)) {
            TUnion::class => $this->buildRandomUnion($node, fn (GraphNode $node) => $this->buildRecursively($node, $with, $classBuilder, $visited)),
            TArray::class => $this->buildRandomArray($node, fn (GraphNode $node) => $this->buildRecursively($node, $with, $classBuilder, $visited)),
            TClass::class => $classBuilder($node, $with, $visited),
            default => $this->buildSingleRandomValue($node->type)
        };
    }

    private function buildSingleRandomValue(TClass|TArray|TUnion|TEnum|TScalar|TNull $type): string|int|float|bool|object|array|null
    {
        return match (get_class($type)) {
            TEnum::class => $type->pickRandomCase(),
            TNull::class => null,
            TScalar::class => match ($type) {
                TScalar::string => $this->faker->text(),
                TScalar::int => $this->faker->numberBetween(PHP_INT_MIN, PHP_INT_MAX), // TODO: Use Randomizer if PHP>=8.2
                TScalar::float =>  $this->faker->randomFloat(), // TODO: negative float values
                TScalar::bool => $this->faker->boolean(),
            },
        };
    }

    private function buildRandomArray(GraphNode $arrayNode, callable $builder): array
    {
        $minElements = 0;
        $maxElements = 50;
        $elementsCount = $this->faker->numberBetween($minElements, $maxElements);
        $array = [];
        for ($i = 0; $i < $elementsCount; $i++) {
            $array[] = $builder($arrayNode->pickRandomBranch());
        }
        return $array;
    }

    private function buildRandomUnion(GraphNode $node, callable $builder)
    {
        return $builder($node->adjacencyList[array_rand($node->adjacencyList)]);
    }

    public function buildRandomClassThroughConstructor(GraphNode $node, array $with, array $visited): object
    {
        $arguments = [];
        foreach ($node->adjacencyList as $paramName => $adj) {
            if (isset($with[$paramName])) { // TODO: this could lead to strange results, as with can modify nested classes properties
                $arguments[] = $with[$paramName];
            }

            if ($adj->type instanceof TClass && isset($visited[$adj->type->class])) {
                $arguments[] = $visited[$adj->type->class];
                continue;
            }

            $classBuilder = $this->getRandomClassBuilderThroughConstructor();
            $value = $this->buildRecursively($adj, $with, $classBuilder, $visited);
            if ($value instanceof TClass) {
                $visited[$adj->type->class] = $value;
            }

            $arguments[] = $value; // TODO: Reuse built objects
        }

        // TODO: constructor could be private/protected. Use named constructor instead
        return new $node->type->class(...$arguments);
    }

    public function buildRandomClassThroughProperties(GraphNode $node, array $with, array $visited): string|object
    {
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

            $classBuilder = $this->getRandomClassBuilderThroughProperties();
            $value = $this->buildRecursively($adj, $with, $classBuilder, $visited);
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

    private function getRandomClassBuilderThroughConstructor(): callable
    {
        return fn($node, $with, $visited) => $this->buildRandomClassThroughConstructor($node, $with, $visited);
    }

    public function getRandomClassBuilderThroughProperties(): callable
    {
        return fn($node, $with, $visited) => $this->buildRandomClassThroughProperties($node, $with, $visited);
    }
}
