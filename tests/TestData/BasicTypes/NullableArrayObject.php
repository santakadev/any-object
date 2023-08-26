<?php

namespace Santakadev\AnyStub\Tests\TestData\BasicTypes;

class NullableArrayObject
{
    public function __construct(public readonly ?array $value)
    {
    }
}
