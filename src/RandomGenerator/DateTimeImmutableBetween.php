<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use DateTimeImmutable;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class DateTimeImmutableBetween implements RandomSpec, RandomCodeGenSpec
{
    public function __construct(
        private readonly string $startDate = '-30 years',
        private readonly string $endDate = '+30 years',
    ) {
    }

    public function generate(): DateTimeImmutable
    {
        $start = strtotime($this->startDate);
        $end = strtotime($this->endDate);
        return new DateTimeImmutable('@' . mt_rand($start, $end));
    }

    public function generateCode(BuilderFactory $factory): Expr
    {
        return $factory->new('DateTimeImmutable', [
            new Expr\BinaryOp\Concat(
                $factory->constFetch(new Name("'@'")),
                $factory->funcCall(
                    'mt_rand',
                    [
                        $factory->funcCall('strtotime', [$factory->constFetch(new Name("'$this->startDate'"))]),
                        $factory->funcCall('strtotime', [$factory->constFetch(new Name("'$this->endDate'"))]),
                    ]
                )
            )
        ]);
    }
}
