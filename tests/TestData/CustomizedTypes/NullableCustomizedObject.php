<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\Faker\NumberBetween;

class NullableCustomizedObject
{
    #[NumberBetween(min: 5, max: 7)]
    public readonly ?int $value;

    public function __construct(
        #[NumberBetween(min: 5, max: 7)]
        ?int $value,
    ) {
        $this->value = $value;
    }
}
