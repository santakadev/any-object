<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class GenericArrayOfUnionBasicTypesObject
{
    public function __construct(
        /** @var array<string|int|float|bool> */
        public readonly array $value)
    {
    }
}
