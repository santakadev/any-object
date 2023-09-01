<?php

namespace Santakadev\AnyObject\Tests\TestData\BasicTypes;

class IntObject
{
    public function __construct(
        public readonly int $value
    ) {
    }
}
