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

    public function buildRandomValue(ReflectionProperty $reflectionProperty, array $visited): string|int|float|bool|object|null
    {
        // TODO: support of array
        $type = $reflectionProperty->getType();

        if ($type === null) {
            // TODO: More detailed error message
            throw new Exception('Untyped properties are not supported');
        }

        if ($type instanceof ReflectionUnionType) {
            $unionTypeNames = array_map(fn($x) => $x->getName(), $type->getTypes());
            $randomArrayKey = array_rand($unionTypeNames);
            $pickedTypeName = $unionTypeNames[$randomArrayKey];
            return $this->buildSingleRandomValue($pickedTypeName, $visited);
        } else if ($type instanceof ReflectionIntersectionType) {
            // TODO: support of intersection types
            throw new Exception('Intersection types are not supported yet');
        } else {
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
}
