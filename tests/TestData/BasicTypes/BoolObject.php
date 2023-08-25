<?php

namespace Santakadev\AnyStub\Tests\TestData\BasicTypes;

class BoolObject
{
    public function __construct(
        public readonly bool $value
    ) {
    }
}
