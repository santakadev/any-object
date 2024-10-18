<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\Utils;

class ArrayUtils
{
    public static function array_some(array $array, $fn): bool
    {
        return count(array_filter($array, $fn)) > 0;
    }
}
