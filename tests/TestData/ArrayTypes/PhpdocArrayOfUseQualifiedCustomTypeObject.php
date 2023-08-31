<?php

namespace Santakadev\AnyStub\Tests\TestData\ArrayTypes;

class PhpdocArrayOfUseQualifiedCustomTypeObject
{
    public function __construct(
        /** @var NonQualifiedObject[] */
        public readonly array $value
    ) {
    }
}
