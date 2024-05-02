<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\NumberBetween;

class NumberBetweenCustomizedObject
{
    #[NumberBetween(min: 5, max: 7)]
    public readonly int $value;

    #[NumberBetween(min: PHP_INT_MIN, max: PHP_INT_MAX)]
    public readonly int $value2;

    public function __construct(
        #[NumberBetween(min: 5, max: 7)]
        int $value,
        int $value2,
    ) {
        $this->value = $value;
        $this->value2 = $value2;
    }
}
