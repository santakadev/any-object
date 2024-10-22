<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart;

use Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart\Money\Money;

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
