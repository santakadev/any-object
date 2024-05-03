<?php

namespace Santakadev\AnyObject\Tests\TestData\UnsupportedTypes;

class ProtectedConstructorObject
{
    protected function __construct(public readonly int $value)
    {
    }
}
