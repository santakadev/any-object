<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;

final class RandomBoolean implements RandomSpec, RandomCodeGenSpec
{
    public function __construct()
    {
    }

    public function generate(): bool
    {
        return (bool) mt_rand(0, 1);
    }

    public function generateCode(BuilderFactory $factory): Expr
    {
        return new Expr\Cast\Bool_(
            $factory->funcCall(
                'mt_rand',
                [
                    $factory->constFetch(new Name('0')),
                    $factory->constFetch(new Name('1')),
                ]
            )
        );
    }
}
