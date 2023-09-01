<?php

namespace Santakadev\AnyObject;

use Exception;
use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionProperty;
use ReflectionUnionType;

class AnyObject
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

    // TODO: support of associative arrays
    public function buildRandomValue(ReflectionProperty $reflectionProperty, array $visited): string|int|float|bool|object|array|null
    {
        $type = $reflectionProperty->getType();

        if ($type === null) {
            throw new Exception(sprintf('Missing type declaration for property "%s"', $reflectionProperty->getName()));
        }

        if ($type instanceof ReflectionUnionType) {
            $unionTypeNames = array_map(fn($x) => $x->getName(), $type->getTypes());
            $pickedTypeName = $this->pickRandomType($unionTypeNames);
            return $this->buildSingleRandomValue($pickedTypeName, $visited);
        } else if ($type instanceof ReflectionIntersectionType) {
            // TODO: support of intersection types
            throw new Exception(sprintf('Intersection type found in property "%s" are not supported', $reflectionProperty->getName()));
        } else {
            if ($type->getName() === 'mixed') {
                throw new Exception("Unsupported type for stub creation: mixed");
            }
            if ($type->getName() === 'array') {
                $phpdocParser = new PhpdocParser();
                $unionTypeNames = $phpdocParser->parseArrayType($reflectionProperty);
                if (false === $unionTypeNames) {
                    throw new Exception(sprintf("Untyped array in %s::%s. Add type Phpdoc typed array comment.", $reflectionProperty->getDeclaringClass()->getName(), $reflectionProperty->getName()));
                }
                $pickedTypeName = $this->pickRandomType($unionTypeNames);
                return $this->buildRandomArrayOf($pickedTypeName, $visited);
            }

            $nullFrequency = 0.5;
            if ($type->allowsNull() && $this->faker->boolean($nullFrequency * 100)) {
                return null;
            }

            return $this->buildSingleRandomValue($type->getName(), $visited);
        }
    }

    public function buildSingleRandomValue(string $typeName, array $visited): string|int|float|bool|object|null
    {
        return match (true) {
            $typeName === 'string' => $this->faker->text(),
            $typeName === 'int' => $this->faker->numberBetween(PHP_INT_MIN, PHP_INT_MAX),
            $typeName === 'float' => $this->faker->randomFloat(), // TODO: negative float values
            $typeName === 'bool' => $this->faker->boolean(),
            $typeName === 'null' => null,
            // TODO: think the best way of handling circular references
            class_exists($typeName) => $visited[$typeName] ?? $this->buildRecursive($typeName, $visited),
            default => throw new Exception("Unsupported type for stub creation: $typeName"),
        };
    }

    public function buildRandomArrayOf(string $typeName, array $visited): array
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

    public function pickRandomType(array $unionTypeNames): mixed
    {
        if (in_array('array', $unionTypeNames)) {
            throw new Exception("Unsupported type array in union types");
        }

        $randomArrayKey = array_rand($unionTypeNames);
        $pickedTypeName = $unionTypeNames[$randomArrayKey];
        return $pickedTypeName;
    }
}
