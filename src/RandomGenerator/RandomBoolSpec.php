<?php

namespace Santakadev\AnyObject\RandomGenerator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;

interface RandomBoolSpec
{
    public function generate(): bool;

    public function generateCode(BuilderFactory $factory): Expr;
}
