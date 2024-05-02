<?php

namespace Santakadev\AnyObject\RandomGenerator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;

interface RandomFloatSpec
{
    public function generate(): float;

    public function generateCode(BuilderFactory $factory): Expr;
}
