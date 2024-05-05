<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use DateTime;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyDateTimeObject;
use Santakadev\AnyObject\Tests\TestData\PhpTypes\DateTimeObject;

class FactoryGeneratorPhpTypesTest extends FactoryGeneratorTestCase
{
    public function test_generator_DateTimeObject(): void
    {
        $this->generateFactoryFor(DateTimeObject::class);

        $text = $this->readGeneratedAnyFileFor(DateTimeObject::class);
        Approvals::verifyString($text);
        $object = AnyDateTimeObject::build();
        $this->assertLessThanOrEqual(new DateTime(), $object->value);
    }
}
