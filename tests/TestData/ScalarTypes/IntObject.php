<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ScalarTypes;

class IntObject
{
    public readonly int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }
}
