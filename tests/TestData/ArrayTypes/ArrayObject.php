<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class ArrayObject
{
    public function __construct(public readonly array $value)
    {
    }
}
