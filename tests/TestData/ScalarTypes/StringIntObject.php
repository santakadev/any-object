<?php

namespace Santakadev\AnyObject\Tests\TestData\ScalarTypes;

class StringIntObject
{
    public readonly string $string;
    public readonly int $number;

    public function __construct(string $string, int $number)
    {
        $this->string = $string;
        $this->number = $number;
    }
}
