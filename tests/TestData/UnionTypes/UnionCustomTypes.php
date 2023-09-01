<?php

namespace Santakadev\AnyObject\Tests\TestData\UnionTypes;

use Santakadev\AnyObject\Tests\TestData\BasicTypes\IntObject;
use Santakadev\AnyObject\Tests\TestData\BasicTypes\StringObject;

class UnionCustomTypes
{
    public function __construct(public readonly StringObject|IntObject $value)
    {
    }
}
