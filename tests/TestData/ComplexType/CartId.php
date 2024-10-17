<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexType;

class CartId
{
    public function __construct(public readonly string $value)
    {
    }
}
