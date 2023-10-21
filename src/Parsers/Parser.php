<?php

namespace Santakadev\AnyObject\Parsers;

use Exception;
use ReflectionEnum;
use ReflectionEnumPureCase;
use ReflectionEnumUnitCase;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TClass;
use Santakadev\AnyObject\Types\TEnum;
use Santakadev\AnyObject\Types\TNull;
use Santakadev\AnyObject\Types\TScalar;
use Santakadev\AnyObject\Types\TUnion;
use function array_map;

class Parser
{
    private PhpdocArrayParser $phpdocArrayParser;

    public function __construct()
    {
        $this->phpdocArrayParser = new PhpdocArrayParser();
    }

    public function typeFromReflection(ReflectionParameter|ReflectionProperty $reflectionParameterOrProperty, string $methodDocComment = null): TUnion|TArray|TEnum|TScalar|TClass
    {
        $reflectionType = $reflectionParameterOrProperty->getType();

        if ($reflectionType === null) {
            throw new Exception(sprintf('Missing type declaration for property "%s"', $reflectionParameterOrProperty->getName()));
        }
        return match (get_class($reflectionType)) {
            ReflectionUnionType::class => $this->parseUnionType($reflectionType),
            ReflectionIntersectionType::class => throw new Exception(sprintf('Intersection type found in property "%s" are not supported', $reflectionParameterOrProperty->getName())),
            ReflectionNamedType::class => $this->typeFromReflectionNamedType($reflectionType, $reflectionParameterOrProperty, $methodDocComment),
        };
    }

    private function typeFromReflectionNamedType(ReflectionNamedType $reflectionType, ReflectionParameter|ReflectionProperty $reflectionParameterOrProperty, ?string $methodDocComment): TUnion|TScalar|TArray|TEnum|TClass
    {
        $typeName = $reflectionType->getName();
        if ($typeName === 'mixed') {
            throw new Exception("Unsupported type for stub creation: mixed");
        }

        if ($typeName === 'array') {
            // TODO: support of associative arrays
            // TODO: array could allow null
            if ($reflectionParameterOrProperty instanceof ReflectionProperty)
                return $this->phpdocArrayParser->parsePropertyArrayType($reflectionParameterOrProperty);
            else {
                return $this->phpdocArrayParser->parseParameterArrayType($reflectionParameterOrProperty, $methodDocComment);
            }
        }

        if (enum_exists($typeName)) {
            // TODO: enum could allow null
            $reflectionEnum = new ReflectionEnum($typeName);
            $reflectionCases = $reflectionEnum->getCases();
            // TODO: Is there any difference with backed enums?
            $cases = array_map(fn(ReflectionEnumUnitCase|ReflectionEnumPureCase $reflectionCase) => $reflectionCase->getValue(), $reflectionCases);
            return new TEnum($cases);
        }

        if (class_exists($typeName)) {
            // TODO: class could allow null
            return new TClass($typeName);
        }

        if (in_array($typeName, TScalar::values())) {
            if ($reflectionType->allowsNull()) {
                return new TUnion([TScalar::from($typeName), new TNull()]);
            } else {
                return TScalar::from($typeName);
            }
        }

        throw new Exception("Unsupported type for stub creation: $typeName");
    }

    /**
     * @param ReflectionUnionType $reflectionType
     * @return TUnion
     */
    public function parseUnionType(ReflectionUnionType $reflectionType): TUnion
    {
        $types = array_map(fn($x) => self::typeFromReflectionTypeForUnion($x), $reflectionType->getTypes());

        return new TUnion($types);
    }

    // TODO: duplicated code
    private static function typeFromReflectionTypeForUnion(ReflectionNamedType $reflectionType): TClass|TEnum|TScalar|TNull
    {
        $typeName = $reflectionType->getName();
        if ($typeName === 'mixed') {
            throw new Exception("Unsupported type for stub creation: mixed");
        }

        if (enum_exists($typeName)) {
            $reflectionEnum = new ReflectionEnum($typeName);
            $reflectionCases = $reflectionEnum->getCases();
            // TODO: Is there any difference with backed enums?
            $cases = array_map(fn(ReflectionEnumUnitCase|ReflectionEnumPureCase $reflectionCase) => $reflectionCase->getValue(), $reflectionCases);
            return new TEnum($cases);
        }

        if (class_exists($typeName)) {
            return new TClass($typeName);
        }

        if (in_array($typeName, TScalar::values())) {
            return TScalar::from($typeName);
        }

        if ($typeName === 'null') {
            return new TNull();
        }

        if ($typeName === 'array') {
            throw new Exception("Unsupported type array in union types");
        }

        throw new Exception("Unsupported type for stub creation: $typeName");
    }
}
