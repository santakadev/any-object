<?php

namespace Santakadev\AnyStub\Tests\TestData\BasicTypes;

class NullableStringObject
{
    public function __construct(public readonly ?string $value)
    {
    }
}
