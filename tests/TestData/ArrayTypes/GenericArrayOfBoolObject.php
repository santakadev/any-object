<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class GenericArrayOfBoolObject
{
    public function __construct(
        /** @var array<bool> */
        public readonly array $value
    ) {
    }
}
