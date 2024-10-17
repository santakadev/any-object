<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\PhpTypes;

use DateTime;

class DateTimeObject
{
    public function __construct(public readonly DateTime $value)
    {
    }
}
