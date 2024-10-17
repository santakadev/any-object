<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class GenericArrayOfStringObject
{
    public function __construct(
        /** @var array<string> */
        public readonly array $value
    ) {
    }
}
