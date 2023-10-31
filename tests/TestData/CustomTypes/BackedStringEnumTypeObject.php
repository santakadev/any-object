<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

class BackedStringEnumTypeObject
{
    public readonly BackedStringEnumType $value;

    public function __construct(BackedStringEnumType $value)
    {
        $this->value = $value;
    }
}
