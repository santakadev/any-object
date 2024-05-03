<?php

namespace Santakadev\AnyObject\Tests\TestData\ComplexContructorTypes;

use Santakadev\AnyObject\Parser\NamedConstructor;

class InvalidNamedConstructorObject
{
    private function __construct(public readonly string $value)
    {
    }

    #[NamedConstructor]
    public function fromString(string $value): self
    {
        return new self($value);
    }
}
