<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

class NullableEnumTypeObject
{
    public readonly ?EnumType $enum;

    public function __construct(?EnumType $enum)
    {
        $this->enum = $enum;
    }
}
