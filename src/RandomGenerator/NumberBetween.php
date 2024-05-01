<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Factory;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class NumberBetween implements RandomIntSpec
{
    public function __construct(
        public readonly int $min,
        public readonly int $max
    ) {
    }

    public function generate(): int
    {
        return (Factory::create())->numberBetween($this->min, $this->max);
    }
}
