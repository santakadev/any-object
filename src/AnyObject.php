<?php

namespace Santakadev\AnyObject;

use Exception;
use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
use ReflectionEnum;
use ReflectionEnumPureCase;
use ReflectionEnumUnitCase;
use ReflectionIntersectionType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TClass;
use Santakadev\AnyObject\Types\TEnum;
use Santakadev\AnyObject\Types\TNull;
use Santakadev\AnyObject\Types\TScalar;
use Santakadev\AnyObject\Types\TUnion;

class AnyObject
{
    private readonly Generator $faker;
    private readonly PhpdocParser $phpdocParser;

    public function __construct(private readonly bool $useConstructor = false)
    {
        $this->faker = Factory::create();
        $this->phpdocParser = new PhpdocParser();
    }

    public function of(string $class, array $with = []): object
    {
        if ($this->useConstructor) {
            return $this->buildFromConstructor($class, $with);
        } else {
            return $this->buildFromProperties($class, $with);
        }
    }

    public function buildFromConstructor(string $class, array $with = [], array $visited = []): object
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor(); // TODO: test an object without constructor. Fallback to properties?
        $constructorParameters = $constructor->getParameters();
        $arguments = [];

        foreach ($constructorParameters as $parameter) {
            // TODO: check if there is a property with the same name and get the type from there? It could be a configuration?
            if ($parameter->isPromoted()) {
                $reflectionProperty = $reflection->getProperty($parameter->getName());
                $type = $this->typeFromReflection($reflectionProperty);
                $arguments[] = $with[$reflectionProperty->getName()] ?? $this->buildSingleRandomValue($type, $visited);
            } else {
                $type = $this->typeFromReflection($parameter, $constructor->getDocComment());
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
            $type = $this->typeFromReflection($reflectionProperty);
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
        if ($type instanceof TArray) {
            return $this->buildRandomArray($type, $visited);
        }

        if ($type instanceof TUnion) {
            return $this->buildSingleRandomValue($type->pickRandom(), $visited);
        }

        if ($type instanceof TEnum) {
            return $type->pickRandom();
        }

        if ($type instanceof TNull) {
            return null;
        }

        if ($type instanceof TScalar) {
            return match ($type) {
                TScalar::string => $this->faker->text(),
                TScalar::int => $this->faker->numberBetween(PHP_INT_MIN, PHP_INT_MAX),
                TScalar::float => $this->faker->randomFloat(), // TODO: negative float values
                TScalar::bool => $this->faker->boolean(),
            };
        }

        if ($type instanceof TClass) {
            // TODO: think the best way of handling circular references
            return $visited[$type->class] ?? $this->buildFromProperties($type->class, [], $visited); // TODO: it could be built from constructor
        }

        throw new Exception("Unreachable code");
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

    private function typeFromReflection(ReflectionParameter|ReflectionProperty $reflectionParameterOrProperty, string $methodDocComment = null): TUnion|TArray|TEnum|TScalar|TClass
    {
        $reflectionType = $reflectionParameterOrProperty->getType();

        if ($reflectionType === null) {
            throw new Exception(sprintf('Missing type declaration for property "%s"', $reflectionParameterOrProperty->getName()));
        }

        if ($reflectionType instanceof ReflectionUnionType) {
            return TUnion::fromReflection($reflectionType);
        } else if ($reflectionType instanceof ReflectionIntersectionType) {
            throw new Exception(sprintf('Intersection type found in property "%s" are not supported', $reflectionParameterOrProperty->getName()));
        } else {
            $typeName = $reflectionType->getName();
            if ($typeName === 'mixed') {
                throw new Exception("Unsupported type for stub creation: mixed");
            }

            if ($typeName === 'array') {
                // TODO: support of associative arrays
                // TODO: array could allow null
                if ($reflectionParameterOrProperty instanceof ReflectionProperty)
                    return $this->phpdocParser->parsePropertyArrayType($reflectionParameterOrProperty);
                else {
                    return $this->phpdocParser->parseParameterArrayType($reflectionParameterOrProperty, $methodDocComment);
                }
            }

            if (enum_exists($typeName)) {
                // TODO: enum could allow null
                return TEnum::fromEnumReference($typeName);
            }

            if (class_exists($typeName)) {
                // TODO: class could allow null
                return new TClass($typeName);
            }

            if (in_array($typeName, ['string', 'int', 'bool', 'float'])) {
                if ($reflectionType->allowsNull()) {
                    return new TUnion([TScalar::from($typeName), new TNull()]);
                } else {
                    return TScalar::from($typeName);
                }
            }

            throw new Exception("Unsupported type for stub creation: $typeName");
        }
    }
}
