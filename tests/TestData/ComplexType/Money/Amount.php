<?php

namespace Santakadev\AnyObject\Tests\TestData\ComplexType\Money;

use Santakadev\AnyObject\RandomGenerator\NumberBetween;

class Amount
{
    public function __construct(#[NumberBetween(1, PHP_INT_MAX)] public readonly int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }
}
