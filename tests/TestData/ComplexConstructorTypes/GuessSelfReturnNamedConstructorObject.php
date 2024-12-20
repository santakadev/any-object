<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes;

class GuessSelfReturnNamedConstructorObject
{
    private function __construct(public readonly string $value)
    {
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
