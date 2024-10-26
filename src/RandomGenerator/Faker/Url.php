<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator\Faker;

use Attribute;
use Faker\Factory;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use Santakadev\AnyObject\RandomGenerator\RandomCodeGenSpec;
use Santakadev\AnyObject\RandomGenerator\RandomSpec;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class Url implements RandomSpec, RandomCodeGenSpec
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
