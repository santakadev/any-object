<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\UnsupportedTypes;

class UnionInterfaceIntObject
{
    public function __construct(public readonly CustomInterface|int $value)
    {
    }
}
