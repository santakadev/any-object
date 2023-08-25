<?php

namespace Santakadev\AnyStub\Tests;

class SubObject
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly Product $product,
    )
    {
    }
}
