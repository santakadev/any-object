<?php

namespace Santakadev\AnyObject\Tests\TestData\BasicTypes;

class StringObject
{
    public function __construct(
        public readonly string $value
    ) {
    }
}
