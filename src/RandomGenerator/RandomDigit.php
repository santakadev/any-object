<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Factory;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class RandomDigit implements RandomIntSpec
{
    public function generate(): int
    {
        return (Factory::create())->randomDigit();
    }

    public function generateCode(BuilderFactory $factory): MethodCall
    {
        return $factory->methodCall(new Variable('faker'), 'randomDigit');
    }
}
