<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Factory;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class RandomFloat implements RandomFloatSpec
{
    public function __construct(
        private readonly ?int $nbMaxDecimals = null,
        private readonly int $min = 0,
        private readonly ?int $max = null,
    ) {
    }

    public function generate(): float
    {
        return (Factory::create())->randomFloat($this->nbMaxDecimals, $this->min, $this->max);
    }
}
