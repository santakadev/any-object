<?php

namespace Santakadev\AnyObject;

use Exception;
use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionProperty;
use ReflectionUnionType;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\Union;

class AnyObject
{
    private readonly Generator $faker;
    private readonly PhpdocParser $phpdocParser;

    public function __construct()
    {
        $this->faker = Factory::create();
        $this->phpdocParser = new PhpdocParser();
    }

    public function of(string $class): object
    {
        return $this->buildRecursive($class);
    }

    private function buildRecursive(string $class, array $visited = []): object
    {
        $reflection = new ReflectionClass($class);
        // TODO: support of constructor arguments instead of properties
        $instance = $reflection->newInstanceWithoutConstructor();
        $visited[$class] = $instance;

        foreach ($reflection->getProperties() as $reflectionProperty) {
            $randomValue = $this->buildRandomValue($reflectionProperty, $visited);

            // Set the random value
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($instance, $randomValue);
            $reflectionProperty->setAccessible(false);
        }

        return $instance;
    }

    // TODO: support enums
    private function buildRandomValue(ReflectionProperty $reflectionProperty, array $visited): string|int|float|bool|object|array|null
    {
        $type = $reflectionProperty->getType();

        if ($type === null) {
            throw new Exception(sprintf('Missing type declaration for property "%s"', $reflectionProperty->getName()));
        }

        if ($type instanceof ReflectionUnionType) {
            $unionType = Union::fromReflection($type);
            return $this->buildSingleRandomValue($unionType, $visited);
        } else if ($type instanceof ReflectionIntersectionType) {
            throw new Exception(sprintf('Intersection type found in property "%s" are not supported', $reflectionProperty->getName()));
        } else {
            if ($type->getName() === 'mixed') {
                throw new Exception("Unsupported type for stub creation: mixed");
            }

            if ($type->getName() === 'array') {
                // TODO: support of associative arrays
                $arrayType = $this->phpdocParser->parseArrayType($reflectionProperty);
                return $this->buildSingleRandomValue($arrayType, $visited);
            }

            $typeName = $type->allowsNull() ? new Union([$type->getName(), 'null']) : $type->getName();

            return $this->buildSingleRandomValue($typeName, $visited);
        }
    }

    private function buildSingleRandomValue(string|TArray|Union $type, array $visited): string|int|float|bool|object|array|null
    {
        if ($type instanceof TArray) {
            return $this->buildRandomArray($type, $visited);
        }

        if ($type instanceof Union) {
            return $this->buildSingleRandomValue($type->pickRandom(), $visited);
        }

        return match (true) {
            $type === 'string' => $this->faker->text(),
            $type === 'int' => $this->faker->numberBetween(PHP_INT_MIN, PHP_INT_MAX),
            $type === 'float' => $this->faker->randomFloat(), // TODO: negative float values
            $type === 'bool' => $this->faker->boolean(),
            $type === 'null' => null,
            // TODO: think the best way of handling circular references
            class_exists($type) => $visited[$type] ?? $this->buildRecursive($type, $visited),
            default => throw new Exception("Unsupported type for stub creation: $type"),
        };
    }

    private function buildRandomArray(TArray $arrayType, array $visited): array
    {
        $minElements = 0;
        $maxElements = 50;
        $elementsCount = $this->faker->numberBetween($minElements, $maxElements);
        $array = [];
        for ($i = 0; $i < $elementsCount; $i++) {
            $array[] = $this->buildSingleRandomValue($arrayType->pickRandom(), $visited);
        }
        return $array;
    }
}
