<?php

namespace Santakadev\AnyObject;

use Exception;
use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
use ReflectionEnum;
use ReflectionEnumBackedCase;
use ReflectionEnumPureCase;
use ReflectionEnumUnitCase;
use ReflectionIntersectionType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TEnum;
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
                $type = $this->typeFromReflectionProperty($reflectionProperty);
                $arguments[] = $with[$reflectionProperty->getName()] ?? $this->buildSingleRandomValue($type, $visited);
            } else {
                $type = $this->typeFromReflectionParameter($parameter, $constructor->getDocComment());
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
            $type = $this->typeFromReflectionProperty($reflectionProperty);
            $value = $with[$reflectionProperty->getName()] ?? $this->buildSingleRandomValue($type, $visited);

            // Set the random value
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($instance, $value);
            $reflectionProperty->setAccessible(false);
        }

        return $instance;
    }

    private function buildSingleRandomValue(string|TArray|TUnion|TEnum $type, array $visited = []): string|int|float|bool|object|array|null
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

        return match (true) {
            $type === 'string' => $this->faker->text(),
            $type === 'int' => $this->faker->numberBetween(PHP_INT_MIN, PHP_INT_MAX),
            $type === 'float' => $this->faker->randomFloat(), // TODO: negative float values
            $type === 'bool' => $this->faker->boolean(),
            $type === 'null' => null,
            // TODO: think the best way of handling circular references
            class_exists($type) => $visited[$type] ?? $this->buildFromProperties($type, [], $visited),
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

    private function typeFromReflectionProperty(ReflectionProperty $reflectionProperty): TUnion|TArray|TEnum|string
    {
        $reflectionType = $reflectionProperty->getType();

        if ($reflectionType === null) {
            throw new Exception(sprintf('Missing type declaration for property "%s"', $reflectionProperty->getName()));
        }

        if ($reflectionType instanceof ReflectionUnionType) {
            return TUnion::fromReflection($reflectionType);
        } else if ($reflectionType instanceof ReflectionIntersectionType) {
            throw new Exception(sprintf('Intersection type found in property "%s" are not supported', $reflectionProperty->getName()));
        } else {
            $typeName = $reflectionType->getName();
            if ($typeName === 'mixed') {
                throw new Exception("Unsupported type for stub creation: mixed");
            }

            if ($typeName === 'array') {
                // TODO: support of associative arrays
                return $this->phpdocParser->parsePropertyArrayType($reflectionProperty);
            }

            if ($reflectionType->allowsNull()) {
                return new TUnion([$typeName, 'null']);
            }

            if (enum_exists($typeName)) {
                $reflectionEnum = new ReflectionEnum($typeName);
                $reflectionCases = $reflectionEnum->getCases();
                // TODO: Is there any difference with backed enums?
                $cases = array_map(fn (ReflectionEnumUnitCase|ReflectionEnumPureCase $reflectionCase) => $reflectionCase->getValue(), $reflectionCases);
                return new TEnum($cases);
            }

            if (class_exists($typeName)) {
                return $typeName;
            }

            if (in_array($typeName, ['string', 'int', 'bool', 'float'])) {
                return $typeName;
            }

            throw new Exception("Unsupported type for stub creation: $typeName");
        }
    }

    private function typeFromReflectionParameter(ReflectionParameter $reflectionParameter, string $methodDocComment): TUnion|TArray|string
    {
        $reflectionType = $reflectionParameter->getType();

        if ($reflectionType === null) {
            throw new Exception(sprintf('Missing type declaration for property "%s"', $reflectionParameter->getName()));
        }

        if ($reflectionType instanceof ReflectionUnionType) {
            return TUnion::fromReflection($reflectionType);
        } else if ($reflectionType instanceof ReflectionIntersectionType) {
            throw new Exception(sprintf('Intersection type found in property "%s" are not supported', $reflectionParameter->getName()));
        } else {
            if ($reflectionType->getName() === 'mixed') {
                throw new Exception("Unsupported type for stub creation: mixed");
            }

            if ($reflectionType->getName() === 'array') {
                // TODO: support of associative arrays
                return $this->phpdocParser->parseParameterArrayType($reflectionParameter, $methodDocComment);
            }

            if ($reflectionType->allowsNull()) {
                return new TUnion([$reflectionType->getName(), 'null']);
            }

            return $reflectionType->getName();
        }
    }
}
