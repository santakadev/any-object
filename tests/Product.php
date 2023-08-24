<?php

namespace Santakadev\AnyStub\Tests;

class Product
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly int $price,
        public readonly float $tax,
        public readonly bool $available,
    ) {
    }
}
