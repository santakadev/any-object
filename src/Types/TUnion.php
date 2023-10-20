<?php

namespace Santakadev\AnyObject\Types;

use Exception;
use ReflectionEnum;
use ReflectionEnumPureCase;
use ReflectionEnumUnitCase;
use ReflectionNamedType;
use ReflectionUnionType;

class TUnion
{
    public function __construct(
        /** @var array<TScalar|TEnum|TArray|string> */
        private readonly array $types
    ) {
        if (in_array('array', $types)) {
            throw new Exception("Unsupported type array in union types");
        }
    }

    public static function fromReflection(ReflectionUnionType $reflectionUnionType): self
    {
        $types = array_map(fn($x) => self::typeFromReflectionType($x), $reflectionUnionType->getTypes());

        return new self($types);
    }

    public static function typeFromReflectionType(ReflectionNamedType $reflectionType): string|TEnum|TScalar
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
            return $typeName;
        }

        if (in_array($typeName, ['string', 'int', 'bool', 'float'])) {
            return TScalar::from($typeName);
        }

        if ($typeName === 'null') {
            return 'null';
        }

        if ($typeName === 'array') {
            throw new Exception("Unsupported type array in union types");
        }

        throw new Exception("Unsupported type for stub creation: $typeName");
    }

    public function pickRandom(): TScalar|TEnum|TArray|string
    {
        return $this->types[array_rand($this->types)];
    }
}
