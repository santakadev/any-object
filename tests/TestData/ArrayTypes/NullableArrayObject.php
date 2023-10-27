<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class NullableArrayObject
{
    public function __construct(public readonly ?array $value)
    {
    }
}
