<?php

namespace Santakadev\AnyObject\Tests\TestData\ScalarTypes;

class StringObject
{
    public readonly string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
