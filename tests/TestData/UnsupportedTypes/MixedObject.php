<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\UnsupportedTypes;

class MixedObject
{
    public function __construct(public mixed $value)
    {
    }
}
