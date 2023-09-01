<?php

namespace Santakadev\AnyObject\Tests\TestData\BasicTypes;

class NullableStringObject
{
    public function __construct(public readonly ?string $value)
    {
    }
}
