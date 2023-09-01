<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomTypes;

class ParentObject
{
    public function __construct(public readonly ChildObject $value)
    {
    }
}
