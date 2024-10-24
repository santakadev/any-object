<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use DateTime;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;

class RandomDateTime implements RandomSpec
{
    public function generate(): DateTime
    {
        $currentDate = time();
        $minTimestamp = strtotime('-30 years', $currentDate);
        $maxTimestamp = strtotime('+30 years', $currentDate);
        $randomTimestamp = mt_rand($minTimestamp, $maxTimestamp);
        return new DateTime('@' . $randomTimestamp);
    }

    public function generateCode(BuilderFactory $factory): Expr
    {
    }
}
