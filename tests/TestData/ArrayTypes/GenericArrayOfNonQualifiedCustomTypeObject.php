<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class GenericArrayOfNonQualifiedCustomTypeObject
{
    public function __construct(
        /** @var array<NonQualifiedObject> */
        public readonly array $value
    ) {
    }
}
