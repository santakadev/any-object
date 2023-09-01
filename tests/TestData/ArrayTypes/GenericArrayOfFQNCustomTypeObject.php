<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class GenericArrayOfFQNCustomTypeObject
{
    public function __construct(
        /** @var array<\Santakadev\AnyObject\Tests\TestData\CustomTypes\ParentObject> */
        public readonly array $value
    ) {
    }
}
