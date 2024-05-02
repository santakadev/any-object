<?php

namespace Santakadev\AnyObject\RandomGenerator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;

interface RandomIntSpec
{
    public function generate(): int;

    public function generateCode(BuilderFactory $factory): Expr;
}
