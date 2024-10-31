<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\Faker\Faker;

class RandomDigitCustomizedObject
{
    #[Faker("randomDigit")]
    public readonly int $value;

    public function __construct(
        #[Faker("randomDigit")]
        int $value
    ) {
        $this->value = $value;
    }
}
