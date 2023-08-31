<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

class PhpdocArrayOfStringObject
{
    public function __construct(
        /** @var string[] */
        public readonly array $value
    ) {
    }
}
