<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Generator;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class RandomFloat implements RandomGenerator
{
    public function __construct(
        private readonly ?int $nbMaxDecimals = null,
        private readonly int $min = 0,
        private readonly ?int $max = null,
    ) {
    }

    public function generate(Generator $faker)
    {
        return $faker->randomFloat($this->nbMaxDecimals, $this->min, $this->max);
    }
}
