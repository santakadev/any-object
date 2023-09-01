<?php

namespace Santakadev\AnyObject\Tests\TestData\BasicTypes;

class FloatObject
{
    public function __construct(
        public readonly float $value
    ) {
    }
}
