<?php

namespace Santakadev\AnyObject\Tests\TestData\IntersectionTypes;

use Countable;
use Iterator;

class IntersectionObject
{
    public function __construct(public readonly Iterator&Countable $value)
    {
    }
}
