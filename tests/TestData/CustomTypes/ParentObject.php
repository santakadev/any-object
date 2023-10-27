<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

class ParentObject
{
    public readonly ChildObject $value;

    public function __construct(ChildObject $value)
    {
        $this->value = $value;
    }
}
