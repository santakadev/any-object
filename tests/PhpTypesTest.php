<?php

namespace Santakadev\AnyObject\Tests;

use DateTime;
use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\PhpTypes\DateTimeObject;

class PhpTypesTest extends AnyObjectTestCase
{
    /** @dataProvider anyProvider */
    public function test_datetime(AnyObject $any): void
    {
        $object = $any->of(DateTimeObject::class);

        $this->assertLessThanOrEqual(new DateTime(), $object->value);
    }
}
