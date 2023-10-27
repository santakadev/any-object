<?php

namespace Santakadev\AnyObject\Tests\TestData\UnsupportedTypes;

class MixedObject
{
    public function __construct(public mixed $value)
    {
    }
}
