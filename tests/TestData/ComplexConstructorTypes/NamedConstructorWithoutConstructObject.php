<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes;

use Santakadev\AnyObject\Parser\NamedConstructor;

class NamedConstructorWithoutConstructObject
{
    public readonly string $value;

    #[NamedConstructor] public static function fromString(string $value): self
    {
        $object = new self();
        $object->value = $value;
        return $object;
    }
}
