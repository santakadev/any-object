<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Factory;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class RandomFloat implements RandomFloatSpec
{
    public function __construct(
        private readonly ?int $nbMaxDecimals = null,
        private readonly int $min = 0,
        private readonly ?int $max = null,
    ) {
    }

    public function generate(): float
    {
        return (Factory::create())->randomFloat($this->nbMaxDecimals, $this->min, $this->max);
    }

    public function generateCode(BuilderFactory $factory): Expr
    {
        return $factory->methodCall(new Variable('faker'), 'randomFloat', [$this->nbMaxDecimals, $this->min, $this->max]);
    }
}
