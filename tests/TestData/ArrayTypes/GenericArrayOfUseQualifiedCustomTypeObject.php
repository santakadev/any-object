<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;

class GenericArrayOfUseQualifiedCustomTypeObject
{
    public function __construct(
        /** @var array<CustomObject> */
        public readonly array $value
    ) {
    }
}
