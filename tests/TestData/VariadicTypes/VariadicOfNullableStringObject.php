<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\VariadicTypes;

class VariadicOfNullableStringObject
{
    /** @var array<string|null>  */
    public array $value;

    public function __construct(?string ...$value)
    {
        $this->value = $value;
    }
}
