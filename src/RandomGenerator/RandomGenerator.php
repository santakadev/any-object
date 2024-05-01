<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Faker\Generator;

interface RandomGenerator
{
    public function generate(Generator $faker);
}
