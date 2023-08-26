<?php

namespace Santakadev\AnyStub\Tests\TestData\UnionTypes;

class UnionArrayIntObject
{
    public function __construct(public readonly array|int $value)
    {
    }
}
