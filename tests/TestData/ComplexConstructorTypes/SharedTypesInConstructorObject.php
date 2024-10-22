<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes;

use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class SharedTypesInConstructorObject
{
    public function __construct(
        public readonly StringObject $value1,
        public readonly StringObject $value2,
        public readonly StringObject $value3,
    ) {
        
    }
}
