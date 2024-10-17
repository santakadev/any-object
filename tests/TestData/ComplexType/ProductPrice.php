<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexType;

use Santakadev\AnyObject\Tests\TestData\ComplexType\Money\Money;

class ProductPrice
{
    public function __construct(public readonly Money $value)
    {
    }

    public function multiply(Quantity $quantity): Money
    {
        return $this->value->multiply($quantity->value);
    }
}
