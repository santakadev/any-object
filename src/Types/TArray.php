<?php

namespace Santakadev\AnyObject\Types;

class TArray
{
    public function __construct(
        private readonly TUnion $union
    ) {
    }

    public function pickRandom(): string
    {
        return $this->union->pickRandom();
    }
}
