<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Div;
use PhpParser\Node\Expr\BinaryOp\Minus;
use PhpParser\Node\Expr\BinaryOp\Mul;
use PhpParser\Node\Expr\BinaryOp\Plus;
use PhpParser\Node\Expr\BinaryOp\ShiftLeft;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\LNumber;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class RandomFloat implements RandomSpec, RandomCodeGenSpec
{
    public function __construct(
        private readonly float $min = PHP_FLOAT_MIN,
        private readonly float $max = PHP_FLOAT_MAX,
    ) {
    }

    public function generate(): float
    {
        // algorithm taken from PHP source code https://raw.githubusercontent.com/php/php-src/f5e743a5203c205db3e6e6e909591b1d968c7593/ext/random/randomizer.c
        $randomBits = mt_rand(0, (1 << 53) - 1);
        $normalized = $randomBits / (1 << 53);
        return $this->min + ($this->max - $this->min) * $normalized;
    }

    public function generateCode(BuilderFactory $factory): Expr
    {
        if ($this->min === PHP_FLOAT_MIN) {
            $min = new ConstFetch(new Name('PHP_FLOAT_MIN'));
        } else {
            $min = $this->min;
        }

        if ($this->max === PHP_FLOAT_MAX) {
            $max = new ConstFetch(new Name('PHP_FLOAT_MAX'));
        } else {
            $max = $this->max;
        }

        $one = $factory->constFetch('1');
        $fiftyThree = new ConstFetch(new Name('53'));
        $shiftLeft = new ShiftLeft($one, $fiftyThree);

        $randomBits = $factory->funcCall(
            'mt_rand',
            [
                new LNumber(0),
                new Arg(new Minus($shiftLeft, $one))
            ]
        );

        $normalized = new Div($randomBits, $shiftLeft);

        return new Plus(
            $min,
            new Mul(
                new Minus($max, $min),
                $normalized
            )
        );
    }
}
