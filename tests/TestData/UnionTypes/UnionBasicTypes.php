<?php

namespace Santakadev\AnyStub\Tests\TestData\UnionTypes;

class UnionBasicTypes
{
    public function __construct(public readonly string|int|float|bool $value)
    {
    }
}
