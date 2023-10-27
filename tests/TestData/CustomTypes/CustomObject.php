<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

class CustomObject
{
    public readonly CustomSubObject $value;

    public function __construct(CustomSubObject $value)
    {
        $this->value = $value;
    }
}
