<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\UnsupportedTypes;

class PrivateConstructorObject
{
    private function __construct(public readonly int $value)
    {
    }
}
