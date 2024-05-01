<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Generator;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class NumberBetween implements RandomGenerator
{
    public function __construct(
        private readonly int $min,
        private readonly int $max
    ) {
    }

    public function generate(Generator $faker): int
    {
        return $faker->numberBetween($this->min, $this->max);
    }
}
