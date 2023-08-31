<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

class PhpdocArrayOfCustomFQNTypeObject
{
    public function __construct(
        /** @var \Santakadev\AnyStub\Tests\TestData\CustomTypes\ParentObject[] */
        public readonly array $value
    ) {
    }
}
