<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class PhpdocArrayOfFloatObject
{
    public function __construct(
        /** @var float[] */
        public readonly array $value
    ) {
    }
}
