<?php

namespace Santakadev\AnyObject\Tests\TestData\ScalarTypes;

class StringIntObject
{
    public readonly string $str;
    public readonly int $number;

    public function __construct(string $str, int $number)
    {
        $this->str = $str;
        $this->number = $number;
    }
}
