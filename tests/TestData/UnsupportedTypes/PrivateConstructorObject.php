<?php

namespace Santakadev\AnyObject\Tests\TestData\UnsupportedTypes;

class PrivateConstructorObject
{
    private function __construct(public readonly int $value)
    {
    }
}
