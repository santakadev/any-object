<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ArrayTypes;

class PhpdocArrayOfIntObject
{
    public function __construct(
        /** @var int[] */
        public readonly array $value
    ) {
    }
}
