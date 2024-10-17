<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

class NullableCustomObject
{
    public readonly ?CustomSubObject $value;

    public function __construct(?CustomSubObject $value)
    {
        $this->value = $value;
    }
}
