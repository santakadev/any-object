<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexTypes\Cart;

class CartLineId
{
    public function __construct(public readonly string $value)
    {
    }

    public static function next(): self
    {
        return new self(uniqid());
    }
}
