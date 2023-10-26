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

class AnyObject
{
    private readonly Generator $faker;
    private readonly Parser $parser;

    public function __construct(private readonly bool $useConstructor = false)
    {
        $this->faker = Factory::create();
        $this->parser = new Parser();
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
        return $this->buildRecursivelyThroughConstructor($root, $with);
    }

    private function buildRecursivelyThroughConstructor(GraphNode $node, array $with, array $visited = [])
    {
        // DFS
        if (!$node->type instanceof TClass) {
            return $this->buildSingleRandomValue($node->type);
        }

        $arguments = [];
        foreach ($node->adjacencyList as $paramName => $adj) {
            if (isset($with[$paramName])) { // TODO: this could lead to strange results, as with can modify nested classes properties
                $arguments[] = $with[$paramName];
            }

            if ($adj->type instanceof TClass && isset($visited[$adj->type->class])) {
                $arguments[] = $visited[$adj->type->class];
                continue;
            }

            $value = $this->buildRecursivelyThroughConstructor($adj, $with, $visited);
            if ($value instanceof TClass) {
                $visited[$adj->type->class] = $value;
            }

            $arguments[] = $value; // TODO: Reuse built objects
        }

        // TODO: constructor could be private/protected. Use named constructor instead
        return new $node->type->class(...$arguments);
    }

    private function buildFromProperties(string $class, array $with = [], array $visited = []): object
    {
        $reflection = new ReflectionClass($class);
        // TODO: support of constructor arguments instead of properties
        $instance = $reflection->newInstanceWithoutConstructor();
        $visited[$class] = $instance;

        foreach ($reflection->getProperties() as $reflectionProperty) {
            // TODO: check the type of the property in $with
            $type = $this->parser->typeFromReflection($reflectionProperty);
            $value = $with[$reflectionProperty->getName()] ?? $this->buildSingleRandomValue($type, $visited);

            // Set the random value
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($instance, $value);
            $reflectionProperty->setAccessible(false);
        }

        return $instance;
    }

    private function buildSingleRandomValue(TClass|TArray|TUnion|TEnum|TScalar|TNull $type, array $visited = []): string|int|float|bool|object|array|null
    {
        return match (get_class($type)) {
            TArray::class => $this->buildRandomArray($type, $visited),
            TUnion::class => $this->buildSingleRandomValue($this->pickRandomUnionType($type), $visited), // TODO: The inner call can return a TClass
            TEnum::class => $this->pickRandomEnumCase($type),
            TNull::class => null,
            TScalar::class => match ($type) {
                TScalar::string => $this->faker->text(),
                TScalar::int => $this->faker->numberBetween(PHP_INT_MIN, PHP_INT_MAX),
                TScalar::float => $this->faker->randomFloat(), // TODO: negative float values
                TScalar::bool => $this->faker->boolean(),
            },
            // TODO: think the best way of handling circular references
            TClass::class => $visited[$type->class] ?? $this->buildFromProperties($type->class, [], $visited), // TODO: it could be built from constructor
        };
    }

    private function buildRandomArray(TArray $arrayType, array $visited): array
    {
        $minElements = 0;
        $maxElements = 50;
        $elementsCount = $this->faker->numberBetween($minElements, $maxElements);
        $array = [];
        for ($i = 0; $i < $elementsCount; $i++) {
            $array[] = $this->buildSingleRandomValue($this->pickRandomArrayType($arrayType), $visited);
        }
        return $array;
    }

    private function pickRandomArrayType(TArray $array): TClass|TArray|TUnion|TEnum|TScalar|TNull // TODO: Can this return TUnion?
    {
        return $this->pickRandomUnionType($array->union);
    }

    private function pickRandomUnionType(TUnion $union): TScalar|TEnum|TArray|TNull|TClass
    {
        return $union->types[array_rand($union->types)];
    }

    private function pickRandomEnumCase(TEnum $enum): mixed
    {
        return $enum->values[array_rand($enum->values)];
    }
}
