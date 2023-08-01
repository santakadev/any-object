<?php

namespace Santakadev\AnyObject\Types;

class TArray
{
    public function __construct(
        private readonly Union $union
    ) {
    }

    public function pickRandom(): string
    {
        return $this->union->pickRandom();
    }
}
