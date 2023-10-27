<?php

namespace Santakadev\AnyObject\Tests\TestData\ScalarTypes;

class NullableStringObject
{
    public readonly ?string $value;

    public function __construct(?string $value)
    {
        $this->value = $value;
    }
}
