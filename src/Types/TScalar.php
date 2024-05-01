<?php

namespace Santakadev\AnyObject\Types;

use Santakadev\AnyObject\RandomGenerator\Boolean;
use Santakadev\AnyObject\RandomGenerator\NumberBetween;
use Santakadev\AnyObject\RandomGenerator\RandomFloat;
use Santakadev\AnyObject\RandomGenerator\RandomGenerator;
use Santakadev\AnyObject\RandomGenerator\Text;

enum TScalar: string
{
    case string = 'string';
    case int = 'int';
    case float = 'float';
    case bool = 'bool';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $e) => $e->name, TScalar::cases());
    }

    public function defaultGenerator(): RandomGenerator
    {
        return match ($this) {
            TScalar::int => new NumberBetween(PHP_INT_MIN, PHP_INT_MAX),
            TScalar::bool => new Boolean(),
            TScalar::string => new Text(),
            TScalar::float => new RandomFloat()
        };
    }
}
