<?php

namespace Santakadev\AnyObject\Attributes\Number;

use Attribute;
use Faker\Generator;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class NumberBetween
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
