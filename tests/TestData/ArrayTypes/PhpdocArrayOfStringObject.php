<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class PhpdocArrayOfStringObject
{
    public function __construct(
        /** @var string[] */
        public readonly array $value
    ) {
    }
}
