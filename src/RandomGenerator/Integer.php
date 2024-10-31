<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
final class Integer implements RandomSpec, RandomCodeGenSpec
{
    public function __construct(
        public readonly int $min,
        public readonly int $max
    ) {
    }

    public function generate(): int
    {
        return mt_rand($this->min, $this->max);
    }

    public function generateCode(BuilderFactory $factory): FuncCall
    {
        if ($this->min === PHP_INT_MIN) {
            $min = new ConstFetch(new Name('PHP_INT_MIN'));
        } else {
            $min = $this->min;
        }

        if ($this->max === PHP_INT_MAX) {
            $max = new ConstFetch(new Name('PHP_INT_MAX'));
        } else {
            $max = $this->max;
        }

        return $factory->funcCall('mt_rand', [$min, $max]);
    }
}
