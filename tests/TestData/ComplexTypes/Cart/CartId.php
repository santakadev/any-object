<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart;

class CartId
{
    public function __construct(public readonly string $value)
    {
    }
}
