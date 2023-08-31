<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

class GenericArrayOfCustomFQNTypeObject
{
    public function __construct(
        /** @var array<\Santakadev\AnyStub\Tests\TestData\CustomTypes\ParentObject> */
        public readonly array $value
    ) {
    }
}
