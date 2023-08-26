<?php

namespace Santakadev\AnyStub\Tests\TestData\UnionTypes;

use Santakadev\AnyStub\Tests\TestData\BasicTypes\IntObject;
use Santakadev\AnyStub\Tests\TestData\BasicTypes\StringObject;

class UnionCustomTypes
{
    public function __construct(public readonly StringObject|IntObject $value)
    {
    }
}
