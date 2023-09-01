<?php

namespace Santakadev\AnyObject\Tests\TestData\Untyped;

class MixedObject
{
    public function __construct(public mixed $value)
    {
    }
}
