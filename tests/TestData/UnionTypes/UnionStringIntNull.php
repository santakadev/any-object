<?php

namespace Santakadev\AnyObject\Tests\TestData\UnionTypes;

class UnionStringIntNull
{
    public function __construct(public readonly string|int|null $value)
    {
    }
}
