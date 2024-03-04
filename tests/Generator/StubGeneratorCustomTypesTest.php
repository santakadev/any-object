<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Generator\StubGenerator;
use Santakadev\AnyObject\Tests\AnyObjectTestCase;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomSubObject;

class StubGeneratorCustomTypesTest extends AnyObjectTestCase
{
    public function test_custom_class(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(CustomObject::class);
        Approvals::verifyString($text);
        $object = AnyCustomObject::build();
        $this->assertInstanceOf(CustomSubObject::class, $object->value);
    }
}
