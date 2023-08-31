<?php

namespace Santakadev\AnyStub;

use Exception;
use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionProperty;
use ReflectionUnionType;

class AnyStub
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function of(string $class): object
    {
        return $this->buildRecursive($class);
    }

    public function buildRecursive(string $class, array $visited = []): object
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

    // TODO: support of array
    public function buildRandomValue(ReflectionProperty $reflectionProperty, array $visited): string|int|float|bool|object|array|null
    {
        $type = $reflectionProperty->getType();

        if ($type === null) {
            throw new Exception(sprintf('Missing type declaration for property "%s"', $reflectionProperty->getName()));
        }

        if ($type instanceof ReflectionUnionType) {
            $unionTypeNames = array_map(fn($x) => $x->getName(), $type->getTypes());
            if (in_array('array', $unionTypeNames)) {
                throw new Exception("Unsupported type for stub creation: array");
            }

            $randomArrayKey = array_rand($unionTypeNames);
            $pickedTypeName = $unionTypeNames[$randomArrayKey];
            if ($pickedTypeName === 'null') {
                return null;
            }

            return $this->buildSingleRandomValue($pickedTypeName, $visited);
        } else if ($type instanceof ReflectionIntersectionType) {
            // TODO: support of intersection types
            throw new Exception(sprintf('Intersection type found in property "%s" are not supported', $reflectionProperty->getName()));
        } else {
            if ($type->getName() === 'mixed') {
                throw new Exception("Unsupported type for stub creation: mixed");
            }
            if ($type->getName() === 'array') {
                $docblock = $reflectionProperty->getDocComment();

                if (preg_match('/@var\s+array<([^\s]+)>/', $docblock, $matches) === 1 ) {
                    return $this->buildRandomArray($matches[1], $visited);
                } else if (preg_match('/@var\s+([^\s]+)\[]/', $docblock, $matches) === 1 ) {
                    return $this->buildRandomArray($matches[1], $visited);
                }

                throw new Exception("Unsupported type for stub creation: array");
            }

            $nullFrequency = 0.5;
            if ($type->allowsNull() && $this->faker->boolean($nullFrequency * 100)) {
                return null;
            }

            return $this->buildSingleRandomValue($type->getName(), $visited);
        }
    }

    public function buildSingleRandomValue(string $typeName, array $visited): string|int|float|bool|object
    {
        return match (true) {
            $typeName === 'string' => $this->faker->text(),
            $typeName === 'int' => $this->faker->numberBetween(PHP_INT_MIN, PHP_INT_MAX),
            $typeName === 'float' => $this->faker->randomFloat(), // TODO: negative float values
            $typeName === 'bool' => $this->faker->boolean(),
            // TODO: think the best way of handling circular references
            class_exists($typeName) => $visited[$typeName] ?? $this->buildRecursive($typeName, $visited),
            default => throw new Exception("Unsupported type for stub creation: $typeName"),
        };
    }

    public function buildRandomArray(string $typeName, array $visited): array
    {
        $minElements = 0;
        $maxElements = 50;
        $elementsCount = $this->faker->numberBetween($minElements, $maxElements);
        $array = [];
        for ($i = 0; $i < $elementsCount; $i++) {
            $array[] = $this->buildSingleRandomValue($typeName, $visited);
        }
        return $array;
    }
}
