<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class PhpdocArrayOfBoolObject
{
    public function __construct(
        /** @var bool[] */
        public readonly array $value
    ) {
    }
}
