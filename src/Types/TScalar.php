<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Types;

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
}
