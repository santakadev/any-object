<?php

namespace Santakadev\AnyObject\Types;

class TArray
{
    public function __construct(
        public readonly TUnion $union
    ) {
    }
}
