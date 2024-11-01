<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\RandomInteger;

class NumberBetweenCustomizedObject
{
    #[RandomInteger(min: 5, max: 7)]
    public readonly int $value;

    #[RandomInteger(min: PHP_INT_MIN, max: PHP_INT_MAX)]
    public readonly int $value2;

    public function __construct(
        #[RandomInteger(min: 5, max: 7)]
        int $value,
        int $value2,
    ) {
        $this->value = $value;
        $this->value2 = $value2;
    }
}
