<?php

namespace Santakadev\AnyObject\Tests\TestData\UnionTypes;

use Santakadev\AnyObject\Tests\TestData\ScalarTypes\IntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class UnionCustomTypes
{
    public function __construct(public readonly StringObject|IntObject $value)
    {
    }
}
