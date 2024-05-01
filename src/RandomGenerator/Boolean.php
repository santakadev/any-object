<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Factory;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class Boolean implements RandomBoolSpec
{
    public function __construct(private readonly int $chanceOfGettingTrue = 50)
    {
    }

    public function generate(): bool
    {
        return (Factory::create())->boolean($this->chanceOfGettingTrue);
    }
}
