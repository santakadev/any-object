<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

use Santakadev\AnyStub\Tests\TestData\CustomTypes\ParentObject;

class PhpdocArrayOfUseQualifierCustomTypeObject
{
    public function __construct(
        /** @var ParentObject[] */
        public readonly array $value
    ) {
    }
}
