<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexContructorTypes;

use Santakadev\AnyObject\Parser\NamedConstructor;

class VariadicNamedConstructorObject
{
    /** @var string[] */
    public array $value;

    private function __construct(string ...$value)
    {
        $this->value = $value;
    }

    #[NamedConstructor]
    public static function fromString(string ...$value): self
    {
        return new self(...$value);
    }
}
