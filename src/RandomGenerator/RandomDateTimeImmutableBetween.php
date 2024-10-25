<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use DateTimeImmutable;
use Faker\Factory;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;

class RandomDateTimeImmutableBetween implements RandomSpec, RandomCodeGenSpec
{
    public function __construct(
        private readonly string $startDate = '-30 years',
        private readonly string $endDate = '+30 years',
    ) {
    }

    public function generate(): DateTimeImmutable
    {
        $dateTime = (Factory::create())->dateTimeBetween($this->startDate, $this->endDate);
        return DateTimeImmutable::createFromMutable($dateTime);
    }

    public function generateCode(BuilderFactory $factory): Expr
    {
        $minInterval = new ConstFetch(new Name("'$this->startDate'"));
        $maxInterval = new ConstFetch(new Name("'$this->endDate'"));

        $fakerDateTimeCall = $factory->methodCall(
            new Variable('faker'),
            'dateTimeBetween',
            [$minInterval, $maxInterval]
        );

        return $factory->staticCall(
            new Name('DateTimeImmutable'),
            'createFromMutable',
            [$fakerDateTimeCall]
        );
    }
}
