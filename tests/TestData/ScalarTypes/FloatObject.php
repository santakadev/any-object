<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ScalarTypes;

class FloatObject
{
    public readonly float $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }
}
