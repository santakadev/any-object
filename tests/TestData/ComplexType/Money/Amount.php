<?php

namespace Santakadev\AnyObject\Tests\TestData\ComplexType\Money;

class Amount
{
    public function __construct(public readonly int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }
}
