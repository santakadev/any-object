<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\VariadicTypes;

class VariadicOfNullableString
{
    /** @var array<string|null>  */
    public array $value;

    public function __construct(?string ...$value)
    {
        $this->value = $value;
    }
}
