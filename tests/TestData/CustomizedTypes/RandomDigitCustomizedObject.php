<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\RandomDigit;

class RandomDigitCustomizedObject
{
    #[RandomDigit]
    public readonly int $value;

    public function __construct(
        #[RandomDigit]
        int $value
    ) {
        $this->value = $value;
    }
}
