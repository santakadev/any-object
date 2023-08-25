<?php

namespace Santakadev\AnyStub\Tests\TestData\CustomTypes;

class ChildObject
{
    public function __construct(public readonly ParentObject $value)
    {
    }
}
