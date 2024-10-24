<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes;

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
