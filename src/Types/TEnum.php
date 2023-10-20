<?php

namespace Santakadev\AnyObject\Types;

class TEnum
{
    public function __construct(private readonly array $values)
    {
    }

    public function pickRandom(): mixed
    {
        return $this->values[array_rand($this->values)];
    }
}
