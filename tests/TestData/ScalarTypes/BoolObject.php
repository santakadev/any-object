<?php

namespace Santakadev\AnyObject\Tests\TestData\ScalarTypes;

class BoolObject
{
    public readonly bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }
}
