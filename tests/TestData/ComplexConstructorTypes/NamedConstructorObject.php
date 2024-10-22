<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes;

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
