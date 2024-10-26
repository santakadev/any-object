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
class Faker implements RandomSpec, RandomCodeGenSpec
{
    private readonly array $args;

    public function __construct(private readonly string $name, ...$args)
    {
        $this->args = $args;
    }

    public function generate()
    {
        return Factory::create()->{$this->name}(...$this->args);
    }

    public function generateCode(BuilderFactory $factory): Expr
    {
        return $factory->methodCall(new Variable('faker'), $this->name, ...$this->args);
    }
}
