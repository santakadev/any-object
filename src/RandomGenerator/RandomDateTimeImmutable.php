<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use DateTimeImmutable;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;

class RandomDateTimeImmutable implements RandomSpec
{
    public function generate(): DateTimeImmutable
    {
        $currentDate = time();
        $minTimestamp = strtotime('-30 years', $currentDate);
        $maxTimestamp = strtotime('+30 years', $currentDate);
        $randomTimestamp = mt_rand($minTimestamp, $maxTimestamp);
        return new DateTimeImmutable('@' . $randomTimestamp);
    }

    public function generateCode(BuilderFactory $factory): Expr
    {
    }
}
