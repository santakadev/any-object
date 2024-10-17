<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

class ChildObject
{
    public readonly ParentObject $value;

    public function __construct(ParentObject $value)
    {
        $this->value = $value;
    }
}
