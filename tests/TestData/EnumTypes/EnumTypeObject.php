<?php

namespace Santakadev\AnyObject\Tests\TestData\EnumTypes;

class EnumTypeObject
{
    public function __construct(public readonly EnumType $enum)
    {
    }
}
