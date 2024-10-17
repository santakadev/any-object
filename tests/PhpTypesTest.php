<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests;

use DateTime;
use DateTimeImmutable;
use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\PhpTypes\DateTimeImmutableObject;
use Santakadev\AnyObject\Tests\TestData\PhpTypes\DateTimeObject;

class PhpTypesTest extends AnyObjectTestCase
{
    /** @dataProvider anyProvider */
    public function test_date_time(AnyObject $any): void
    {
        $object = $any->of(DateTimeObject::class);

        $this->assertInstanceOf(DateTime::class, $object->value);
        $this->assertLessThanOrEqual(new DateTime(), $object->value);
    }

    /** @dataProvider anyProvider */
    public function test_date_time_immutable(AnyObject $any): void
    {
        $object = $any->of(DateTimeImmutableObject::class);

        $this->assertInstanceOf(DateTimeImmutable::class, $object->value);
        $this->assertLessThanOrEqual(new DateTimeImmutable(), $object->value);
    }
}
