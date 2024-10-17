<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class GenericArrayOfUnionBasicTypesObject
{
    public function __construct(
        /** @var array<string|int|float|bool> */
        public readonly array $value)
    {
    }
}
