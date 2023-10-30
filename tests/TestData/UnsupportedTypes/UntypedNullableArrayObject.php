<?php

namespace Santakadev\AnyObject\Tests\TestData\UnsupportedTypes;

class UntypedNullableArrayObject
{
    public function __construct(public readonly ?array $value)
    {
    }
}
