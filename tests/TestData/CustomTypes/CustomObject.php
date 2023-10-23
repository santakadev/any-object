<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

class CustomObject
{
    public function __construct(public readonly CustomSubObject $value)
    {
    }
}
