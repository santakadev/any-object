<?php

namespace Santakadev\AnyObject\Tests\TestData\UnsupportedTypes;

class InterfaceObject
{
    public function __construct(public readonly CustomInterface $value)
    {
    }
}
