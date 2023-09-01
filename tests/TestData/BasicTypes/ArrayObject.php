<?php

namespace Santakadev\AnyObject\Tests\TestData\BasicTypes;

class ArrayObject
{
    public function __construct(public readonly array $value)
    {
    }
}
