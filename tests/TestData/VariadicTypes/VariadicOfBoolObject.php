<?php

namespace Santakadev\AnyObject\Tests\TestData\VariadicTypes;

class VariadicOfBoolObject
{
    /** @var bool[] */
    public readonly array $value;

    public function __construct(bool ...$value)
    {
        $this->value = $value;
    }
}
