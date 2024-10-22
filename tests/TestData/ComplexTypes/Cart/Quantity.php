<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart;

class Quantity
{
    public function __construct(public readonly int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative');
        }
    }
}
