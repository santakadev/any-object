<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Factory;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class Text implements RandomStringSpec
{
    public function __construct(private readonly int $maxNbChars = 200)
    {
    }

    public function generate(): string
    {
        return (Factory::create())->text($this->maxNbChars);
    }
}
