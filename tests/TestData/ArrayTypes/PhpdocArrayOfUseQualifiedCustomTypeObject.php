<?php

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class PhpdocArrayOfUseQualifiedCustomTypeObject
{
    public function __construct(
        /** @var NonQualifiedObject[] */
        public readonly array $value
    ) {
    }
}
