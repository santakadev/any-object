<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Generator;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class Boolean implements RandomGenerator
{
    public function __construct(private readonly int $chanceOfGettingTrue = 50)
    {
    }

    public function generate(Generator $faker): bool
    {
        return $faker->boolean($this->chanceOfGettingTrue);
    }
}
