<?php

namespace Santakadev\AnyObject\Tests\TestData\ComplexContructorTypes;

use Santakadev\AnyObject\Parser\NamedConstructor;

class NamedConstructorObject
{
    private function __construct(public readonly string $value)
    {
    }

    #[NamedConstructor]
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
