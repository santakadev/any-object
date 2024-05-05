<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Factory;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class Url implements RandomStringSpec
{
    public function generate(): string
    {
        return (Factory::create())->url();
    }

    public function generateCode(BuilderFactory $factory): Expr
    {
        return $factory->methodCall(new Variable('faker'), 'url');
    }
}
