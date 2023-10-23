<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

use Santakadev\AnyObject\Tests\TestData\BasicTypes\StringObject;

class CustomSubObject
{
    public function __construct(public readonly StringObject $value)
    {
    }
}
