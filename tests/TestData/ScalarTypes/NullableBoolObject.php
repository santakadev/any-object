<?php

namespace Santakadev\AnyObject\Tests\TestData\ScalarTypes;

class NullableBoolObject
{
    public readonly ?bool $value;

    public function __construct(?bool $value)
    {
        $this->value = $value;
    }
}
