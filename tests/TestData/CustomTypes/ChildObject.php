<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

class ChildObject
{
    public function __construct(public readonly ParentObject $value)
    {
    }
}
