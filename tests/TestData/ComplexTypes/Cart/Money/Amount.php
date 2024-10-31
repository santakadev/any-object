<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart\Money;

use Santakadev\AnyObject\RandomGenerator\Integer;

class Amount
{
    public function __construct(#[Integer(1, PHP_INT_MAX)] public readonly int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }
}
