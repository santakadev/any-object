<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart;

class ProductId
{
    public function __construct(public readonly string $value)
    {
    }
}
