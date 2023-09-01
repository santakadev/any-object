<?php

namespace Santakadev\AnyObject\Tests\TestData\BasicTypes;

class BoolObject
{
    public function __construct(
        public readonly bool $value
    ) {
    }
}
