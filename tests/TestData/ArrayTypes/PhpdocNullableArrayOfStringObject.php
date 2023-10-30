<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class PhpdocNullableArrayOfStringObject
{
    public function __construct(
        /** @var ?string[] */
        public readonly array $value
    ) {
    }
}
