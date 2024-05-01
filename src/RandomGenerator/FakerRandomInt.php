<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Faker\Factory;
use Faker\Generator;

class FakerRandomInt
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function random(RandomIntSpec $spec): int
    {
        return match (get_class($spec)) {
            NumberBetween::class => $this->faker->numberBetween($spec->min, $spec->max),
            RandomDigit::class => $this->faker->randomDigit()
        };
    }

    public function defaultSpec(): RandomIntSpec
    {
        return new NumberBetween(PHP_INT_MIN, PHP_INT_MAX);
    }
}
