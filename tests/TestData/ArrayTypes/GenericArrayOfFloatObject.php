<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

class GenericArrayOfFloatObject
{
    public function __construct(
        /** @var array<float> */
        public readonly array $value
    ) {
    }
}
