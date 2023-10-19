<?php

namespace Santakadev\AnyObject\Tests\TestData\Constructor;

class NonPromotedConstructor
{
    public readonly string $stringProperty;
    public readonly  int $intProperty;
    public readonly float $floatProperty;
    public readonly bool $boolProperty;
    public readonly ?string $nullableStringProperty;
    public readonly ?int $nullableIntProperty;
    public readonly ?float $nullableFloatProperty;
    public readonly ?bool $nullableBoolProperty;
    public readonly array $arrayProperty;
    public readonly string|int|bool|float $unionTypeProperty;
    public string $nonAssignedProperty = 'nonAssignedProperty';

    /**
     * @param string[] $array
     */
    public function __construct(
        string $string,
        int $int,
        float $float,
        bool $bool,
        ?string $nullableString,
        ?int $nullableInt,
        ?float $nullableFloat,
        ?bool $nullableBool,
        array $array,
        string|int|float|bool $unionType
    ) {
        $this->stringProperty = $string;
        $this->intProperty = $int;
        $this->floatProperty = $float;
        $this->boolProperty = $bool;
        $this->nullableStringProperty = $nullableString;
        $this->nullableIntProperty = $nullableInt;
        $this->nullableFloatProperty = $nullableFloat;
        $this->nullableBoolProperty = $nullableBool;
        $this->arrayProperty = $array;
        $this->unionTypeProperty = $unionType;
    }
}
