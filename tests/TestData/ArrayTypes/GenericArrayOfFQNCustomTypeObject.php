<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class GenericArrayOfFQNCustomTypeObject
{
    public function __construct(
        /** @var array<\Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject> */
        public readonly array $value
    ) {
    }
}
