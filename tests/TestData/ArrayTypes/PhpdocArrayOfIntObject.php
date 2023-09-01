<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class PhpdocArrayOfIntObject
{
    public function __construct(
        /** @var int[] */
        public readonly array $value
    ) {
    }
}
