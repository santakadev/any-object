<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

use Santakadev\AnyObject\Tests\TestData\CustomTypes\ParentObject;

class GenericArrayOfUseQualifiedCustomTypeObject
{
    public function __construct(
        /** @var array<ParentObject> */
        public readonly array $value
    ) {
    }
}
