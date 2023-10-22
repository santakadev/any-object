<?php

namespace Santakadev\AnyObject;

use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
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

    private function buildFromConstructor(string $class, array $with = [], array $visited = []): object
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor(); // TODO: test an object without constructor. Fallback to properties?
        $constructorParameters = $constructor->getParameters();
        $arguments = [];

        foreach ($constructorParameters as $parameter) {
            // TODO: check if there is a property with the same name and get the type from there? It could be a configuration?
            if ($parameter->isPromoted()) {
                $reflectionProperty = $reflection->getProperty($parameter->getName());
                $type = $this->parser->typeFromReflection($reflectionProperty);
                $arguments[] = $with[$reflectionProperty->getName()] ?? $this->buildSingleRandomValue($type, $visited);
            } else {
                $type = $this->parser->typeFromReflection($parameter, $constructor->getDocComment());
                $arguments[] = $this->buildSingleRandomValue($type);
            }
        }

        // TODO: constructor could be private/protected
        return new $class(...$arguments);
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
            TUnion::class => $this->buildSingleRandomValue($this->pickRandomUnionType($type), $visited),
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
