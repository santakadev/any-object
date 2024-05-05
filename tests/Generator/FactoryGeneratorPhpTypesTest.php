<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use DateTime;
use DateTimeImmutable;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyDateTimeImmutableObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyDateTimeObject;
use Santakadev\AnyObject\Tests\TestData\PhpTypes\DateTimeImmutableObject;
use Santakadev\AnyObject\Tests\TestData\PhpTypes\DateTimeObject;

class FactoryGeneratorPhpTypesTest extends FactoryGeneratorTestCase
{
    public function test_generator_date_time(): void
    {
        $this->generateFactoryFor(DateTimeObject::class);

        $text = $this->readGeneratedAnyFileFor(DateTimeObject::class);
        Approvals::verifyString($text);
        $object = AnyDateTimeObject::build();
        $this->assertInstanceOf(DateTime::class, $object->value);
        $this->assertLessThanOrEqual(new DateTime(), $object->value);
    }

    public function test_generator_date_time_immutable(): void
    {
        $this->generateFactoryFor(DateTimeImmutableObject::class);

        $text = $this->readGeneratedAnyFileFor(DateTimeImmutableObject::class);
        Approvals::verifyString($text);
        $object = AnyDateTimeImmutableObject::build();
        $this->assertInstanceOf(DateTimeImmutable::class, $object->value);
        $this->assertLessThanOrEqual(new DateTimeImmutable(), $object->value);
    }
}
