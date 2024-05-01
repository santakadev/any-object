<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\Attributes\Number\NumberBetween;

class NumberBetweenCustomizedObject
{
    #[NumberBetween(min: 5, max: 7)]
    public readonly int $value;

    public function __construct(
        #[NumberBetween(min: 5, max: 7)]
        int $value
    ) {
        $this->value = $value;
    }
}
