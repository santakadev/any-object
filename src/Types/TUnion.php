<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Types;

use Exception;

class TUnion
{
    public function __construct(
        /** @var array<TScalar|TEnum|TArray|TNull|TClass> */
        public readonly array $types
    ) {
        if (in_array('array', $types)) {
            throw new Exception("Unsupported type array in union types");
        }
    }
}
