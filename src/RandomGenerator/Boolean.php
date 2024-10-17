<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Factory;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;

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

    public function generateCode(BuilderFactory $factory): Expr
    {
        return $factory->methodCall(new Variable('faker'), 'boolean', [$this->chanceOfGettingTrue]);
    }
}
