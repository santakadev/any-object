<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

class NullableEnumTypeObject
{
    public readonly ?EnumType $value;

    public function __construct(?EnumType $value)
    {
        $this->value = $value;
    }
}
