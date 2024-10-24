<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;

interface RandomStringSpec
{
    public function generateCode(BuilderFactory $factory): Expr;
}
