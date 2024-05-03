<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNamedConstructorObject;
use Santakadev\AnyObject\Tests\TestData\ComplexContructorTypes\NamedConstructorObject;

class FactoryGeneratorComplexConstructorTypesTest extends FactoryGeneratorTestCase
{
    public function test_generator_from_named_constructor(): void
    {
        $this->generateFactoryFor(NamedConstructorObject::class);

        $text = $this->readGeneratedAnyFileFor(NamedConstructorObject::class);
        Approvals::verifyString($text);
        $test = AnyNamedConstructorObject::with(value: "test");
        $this->assertEquals("test", $test->value);
    }
}
