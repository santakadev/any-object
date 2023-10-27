<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class CustomSubObject
{
    public readonly StringObject $value;

    public function __construct(StringObject $value)
    {
        $this->value = $value;
    }
}
