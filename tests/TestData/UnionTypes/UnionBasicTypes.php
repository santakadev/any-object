<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\UnionTypes;

class UnionBasicTypes
{
    public function __construct(public readonly string|int|float|bool $value)
    {
    }
}
