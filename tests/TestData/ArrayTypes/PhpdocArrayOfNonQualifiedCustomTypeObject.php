<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;

class PhpdocArrayOfNonQualifiedCustomTypeObject
{
    public function __construct(
        /** @var CustomObject[] */
        public readonly array $value
    ) {
    }
}
