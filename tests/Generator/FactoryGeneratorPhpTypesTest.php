<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use DateTime;
use DateTimeImmutable;
use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyDateTimeImmutableObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyDateTimeObject;
use Santakadev\AnyObject\Tests\TestData\PhpTypes\DateTimeImmutableObject;
use Santakadev\AnyObject\Tests\TestData\PhpTypes\DateTimeObject;

class FactoryGeneratorPhpTypesTest extends FactoryGeneratorTestCase
{
    /** @dataProvider anyProvider */
    public function test_date_time(AnyObject $any): void
    {
        $this->generateFactoryFor(DateTimeObject::class);

        $text = $this->readGeneratedAnyFileFor(DateTimeObject::class);
        Approvals::verifyString($text);
        $object = AnyDateTimeObject::build();
        $this->assertInstanceOf(DateTime::class, $object->value);
    }

    /** @dataProvider anyProvider */
    public function test_date_time_immutable(AnyObject $any): void
    {
        $this->generateFactoryFor(DateTimeImmutableObject::class);

        $text = $this->readGeneratedAnyFileFor(DateTimeImmutableObject::class);
        Approvals::verifyString($text);
        $object = AnyDateTimeImmutableObject::build();
        $this->assertInstanceOf(DateTimeImmutable::class, $object->value);
    }
}
