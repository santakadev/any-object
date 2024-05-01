<?php

namespace Santakadev\AnyObject\Attributes\Number;

use Attribute;
use Faker\Generator;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class RandomDigit
{
    public function generate(Generator $faker): int
    {
        return $faker->randomDigit();
    }
}
