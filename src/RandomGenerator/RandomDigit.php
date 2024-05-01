<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Generator;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class RandomDigit implements RandomGenerator
{
    public function generate(Generator $faker): int
    {
        return $faker->randomDigit();
    }
}
