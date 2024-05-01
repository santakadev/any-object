<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Factory;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class RandomDigit implements RandomIntSpec
{
    public function generate(): int
    {
        return (Factory::create())->randomDigit();
    }
}
