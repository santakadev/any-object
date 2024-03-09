<?php

namespace Santakadev\AnyObject\Tests\TestData\ComplexType;

class Quantity
{
    public function __construct(public readonly int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative');
        }
    }
}
