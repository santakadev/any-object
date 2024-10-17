<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\UnsupportedTypes;

class UntypedNullableArrayObject
{
    public function __construct(public readonly ?array $value)
    {
    }
}
