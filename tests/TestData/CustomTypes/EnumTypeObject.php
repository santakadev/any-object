<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

class EnumTypeObject
{
    public readonly EnumType $enum;

    public function __construct(EnumType $enum)
    {
        $this->enum = $enum;
    }
}
