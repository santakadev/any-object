<?php

namespace Santakadev\AnyObject\Types;

use Exception;
use ReflectionUnionType;

class Union
{
    public function __construct(
        /** @var string[] */
        private readonly array $types
    ) {
        if (in_array('array', $types)) {
            throw new Exception("Unsupported type array in union types");
        }
    }

    public static function fromReflection(ReflectionUnionType $reflectionUnionType): self
    {
        return new self(array_map(fn($x) => $x->getName(), $reflectionUnionType->getTypes()));
    }

    public function pickRandom(): string
    {
        return $this->types[array_rand($this->types)];
    }
}
