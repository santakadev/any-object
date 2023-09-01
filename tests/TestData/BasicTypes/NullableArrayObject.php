<?php

namespace Santakadev\AnyObject\Tests\TestData\BasicTypes;

class NullableArrayObject
{
    public function __construct(public readonly ?array $value)
    {
    }
}
