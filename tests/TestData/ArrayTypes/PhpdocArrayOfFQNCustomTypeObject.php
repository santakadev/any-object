<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class PhpdocArrayOfFQNCustomTypeObject
{
    public function __construct(
        /** @var \Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject[] */
        public readonly array $value
    ) {
    }
}
