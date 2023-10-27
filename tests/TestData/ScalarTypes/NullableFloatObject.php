<?php

namespace Santakadev\AnyObject\Tests\TestData\ScalarTypes;

class NullableFloatObject
{
    public readonly ?float $value;

    public function __construct(?float $value)
    {
        $this->value = $value;
    }
}
