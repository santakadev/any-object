<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

use Santakadev\AnyStub\Tests\TestData\CustomTypes\ParentObject;

class PhpdocArrayOfCustomTypeObject
{
    public function __construct(
        /** @var ParentObject[] */
        public readonly array $value
    ) {
    }
}
