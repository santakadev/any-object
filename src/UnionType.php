<?php

namespace Santakadev\AnyObject;

use Exception;

class UnionType
{
    public function __construct(
        /** @var string[] */
        private readonly array $types
    ) {
        if (in_array('array', $types)) {
            throw new Exception("Unsupported type array in union types");
        }
    }

    public function pickRandom(): string
    {
        return $this->types[array_rand($this->types)];
    }
}
