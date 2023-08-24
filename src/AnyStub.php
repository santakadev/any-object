<?php

namespace Santakadev\AnyStub;

use Exception;
use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
use ReflectionProperty;

class AnyStub
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function of(string $class): object
    {
        $reflection = new ReflectionClass($class);
        // TODO: support of constructor arguments instead of properties
        $instance = $reflection->newInstanceWithoutConstructor();

        foreach ($reflection->getProperties() as $reflectionProperty) {
            $randomValue = $this->buildRandomValue($reflectionProperty);

            // Set the random value
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($instance, $randomValue);
            $reflectionProperty->setAccessible(false);
        }

        return $instance;
    }

    public function buildRandomValue(ReflectionProperty $reflectionProperty): string|int
    {
        // TODO: support of nullable
        // TODO: support of array
        // TODO: support of union types
        // TODO: support of intersection types
        // TODO: support of custom types
        $type = $reflectionProperty->getType()->getName();
        return match ($type) {
            'string' => $this->faker->text(),
            'int' => $this->faker->numberBetween(PHP_INT_MIN, PHP_INT_MAX),
            'float' => $this->faker->randomFloat(), // TODO: negative float values
            'bool' => $this->faker->boolean(),
            default => throw new Exception("Unsupported type for stub creation: $type"),
        };
    }
}
