<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class GenericArrayOfFloatObject
{
    public function __construct(
        /** @var array<float> */
        public readonly array $value
    ) {
    }
}
