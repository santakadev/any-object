<?php

namespace Santakadev\AnyObject\Types;

class TEnum
{
    // TODO: I'm not sure about what is the actual type of $values
    public function __construct(public readonly array $values)
    {
    }
}
