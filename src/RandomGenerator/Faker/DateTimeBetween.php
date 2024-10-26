<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator\Faker;

use DateTime;
use Faker\Factory;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use Santakadev\AnyObject\RandomGenerator\RandomCodeGenSpec;
use Santakadev\AnyObject\RandomGenerator\RandomSpec;

class DateTimeBetween implements RandomSpec, RandomCodeGenSpec
{
    public function __construct(
        private readonly string $startDate = '-30 years',
        private readonly string $endDate = '+30 years',
    ) {
    }

    public function generate(): DateTime
    {
        return (Factory::create())->dateTimeBetween($this->startDate, $this->endDate);
    }

    public function generateCode(BuilderFactory $factory): Expr
    {
        $minInterval = new ConstFetch(new Name("'$this->startDate'"));
        $maxInterval = new ConstFetch(new Name("'$this->endDate'"));

        return $factory->methodCall(
            new Variable('faker'),
            'dateTimeBetween',
            [$minInterval, $maxInterval]
        );
    }
}
