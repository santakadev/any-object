<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

class GenericArrayOfNonQualifiedCustomTypeObject
{
    public function __construct(
        /** @var array<NonQualifiedObject> */
        public readonly array $value
    ) {
    }
}
