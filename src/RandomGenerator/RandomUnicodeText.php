<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use IntlChar;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class RandomUnicodeText implements RandomSpec, RandomCodeGenSpec
{
    public function __construct(
        private readonly int $length = 200,
    ) {
    }

    public function generate(): string
    {
        return self::random($this->length);
    }

    public static function random(int $length): string
    {
        $text = '';
        $currentLength = 0;

        while ($currentLength < $length) {
            $codePoint = mt_rand(0x0020, 0x1FAF8); // https://www.compart.com/en/unicode/block

            if (IntlChar::isDefined($codePoint) && IntlChar::isprint($codePoint)) {
                $text .= IntlChar::chr($codePoint);
                $currentLength++;
            }
        }

        return $text;
    }

    public function generateCode(BuilderFactory $factory): Expr
    {
        return $factory->staticCall('\\' . __CLASS__, 'random', [$this->length]);
    }
}
