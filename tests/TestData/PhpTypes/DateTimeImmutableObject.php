<?php

namespace Santakadev\AnyObject\Tests\TestData\PhpTypes;

use DateTimeImmutable;

class DateTimeImmutableObject
{
    public function __construct(public readonly DateTimeImmutable $value)
    {
    }
}
