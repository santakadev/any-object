<?php

namespace Santakadev\AnyObject\RandomGenerator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;

interface RandomStringSpec
{
    public function generate(): string;

    public function generateCode(BuilderFactory $factory): Expr;
}
