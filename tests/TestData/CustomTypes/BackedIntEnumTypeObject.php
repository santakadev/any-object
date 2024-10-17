<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

class BackedIntEnumTypeObject
{
    public readonly BackedIntEnumType $value;

    public function __construct(BackedIntEnumType $value)
    {
        $this->value = $value;
    }
}
