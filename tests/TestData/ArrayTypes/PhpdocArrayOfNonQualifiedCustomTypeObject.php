<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

use Santakadev\AnyObject\Tests\TestData\CustomTypes\ParentObject;

class PhpdocArrayOfNonQualifiedCustomTypeObject
{
    public function __construct(
        /** @var ParentObject[] */
        public readonly array $value
    ) {
    }
}
