<?php

namespace Santakadev\AnyObject\Types;

use ReflectionEnum;
use ReflectionEnumPureCase;
use ReflectionEnumUnitCase;

class TEnum
{
    public function __construct(private readonly array $values)
    {
    }

    public static function fromEnumReference(string $enumReference): self
    {
        $reflectionEnum = new ReflectionEnum($enumReference);
        $reflectionCases = $reflectionEnum->getCases();
        // TODO: Is there any difference with backed enums?
        $cases = array_map(fn (ReflectionEnumUnitCase|ReflectionEnumPureCase $reflectionCase) => $reflectionCase->getValue(), $reflectionCases);
        return new TEnum($cases);
    }

    public function pickRandom(): mixed
    {
        return $this->values[array_rand($this->values)];
    }
}
