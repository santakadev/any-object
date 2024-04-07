<?php

namespace Santakadev\AnyObject\Tests\TestData\PhpTypes;

use DateTime;

class DateTimeObject
{
    public function __construct(public readonly DateTime $value)
    {
    }
}
