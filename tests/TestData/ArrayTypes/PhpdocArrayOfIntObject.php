<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

class PhpdocArrayOfIntObject
{
    public function __construct(
        /** @var int[] */
        public readonly array $value
    ) {
    }
}
