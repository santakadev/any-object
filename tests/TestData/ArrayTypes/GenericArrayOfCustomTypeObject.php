<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

use Santakadev\AnyStub\Tests\TestData\CustomTypes\ParentObject;

class GenericArrayOfCustomTypeObject
{
    public function __construct(
        /** @var array<ParentObject> */
        public readonly array $value
    ) {
    }
}
