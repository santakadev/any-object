<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class NonQualifiedObject
{
    public function __construct(public readonly string $value)
    {
    }
}
