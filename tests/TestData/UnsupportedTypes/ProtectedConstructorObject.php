<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\UnsupportedTypes;

class ProtectedConstructorObject
{
    protected function __construct(public readonly int $value)
    {
    }
}
