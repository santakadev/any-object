<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

class GenericArrayOfIntObject
{
    public function __construct(
        /** @var array<int> */
        public readonly array $value
    ) {
    }
}
