<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

class GenericArrayOfBoolObject
{
    public function __construct(
        /** @var array<bool> */
        public readonly array $value
    ) {
    }
}
