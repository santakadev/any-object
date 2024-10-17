<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\PhpTypes;

use DateTimeImmutable;

class DateTimeImmutableObject
{
    public function __construct(public readonly DateTimeImmutable $value)
    {
    }
}
