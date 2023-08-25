<?php

namespace Santakadev\AnyStub\Tests\TestData\BasicTypes;

class StringObject
{
    public function __construct(
        public readonly string $value
    ) {
    }
}
