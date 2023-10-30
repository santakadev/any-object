<?php

namespace Santakadev\AnyObject\Tests\TestData\UnsupportedTypes;

class UntypedArrayObject
{
    public function __construct(public readonly array $value)
    {
    }
}
