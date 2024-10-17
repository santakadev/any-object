<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class GenericArrayOfIntObject
{
    public function __construct(
        /** @var array<int> */
        public readonly array $value
    ) {
    }
}
