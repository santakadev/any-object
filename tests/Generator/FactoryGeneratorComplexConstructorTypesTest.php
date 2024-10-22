<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNamedConstructorObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyVariadicNamedConstructorObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\NamedConstructorObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\VariadicNamedConstructorObject;

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

    public function test_generator_from_variadic_named_constructor(): void
    {
        $this->generateFactoryFor(VariadicNamedConstructorObject::class);

        $text = $this->readGeneratedAnyFileFor(VariadicNamedConstructorObject::class);
        Approvals::verifyString($text);
        $test = AnyVariadicNamedConstructorObject::with(value: ["a", "b"]);
        $this->assertEquals(["a", "b"], $test->value);
    }
}
