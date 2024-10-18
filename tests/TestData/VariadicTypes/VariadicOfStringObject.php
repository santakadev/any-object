<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\VariadicTypes;

class VariadicOfStringObject
{
    /** @var string[] */
    public readonly array $value;

    public function __construct(string ...$value)
    {
        $this->value = $value;
    }
}
