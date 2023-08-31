<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

class PhpdocArrayOfBoolObject
{
    public function __construct(
        /** @var bool[] */
        public readonly array $value
    ) {
    }
}
