<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Generator;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class Text implements RandomGenerator
{
    public function __construct(private readonly int $maxNbChars = 200)
    {
    }

    public function generate(Generator $faker): string
    {
        return $faker->text($this->maxNbChars);
    }
}
