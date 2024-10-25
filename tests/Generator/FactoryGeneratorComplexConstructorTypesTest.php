<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNamedConstructorWithPrivateConstructObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnySharedTypesInConstructorObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyVariadicNamedConstructorObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\NamedConstructorWithPrivateConstructObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\SharedTypesInConstructorObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\VariadicNamedConstructorObject;

class FactoryGeneratorComplexConstructorTypesTest extends FactoryGeneratorTestCase
{
    public function test_generator_from_named_constructor(): void
    {
        $this->generateFactoryFor(NamedConstructorWithPrivateConstructObject::class);

        $text = $this->readGeneratedAnyFileFor(NamedConstructorWithPrivateConstructObject::class);
        Approvals::verifyString($text);
        $test = AnyNamedConstructorWithPrivateConstructObject::with(value: "test");
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

    public function test_generator_shared_types_in_constructor(): void
    {
        $this->generateFactoryFor(SharedTypesInConstructorObject::class);

        $text = $this->readGeneratedAnyFileFor(SharedTypesInConstructorObject::class);
        Approvals::verifyString($text);
        AnySharedTypesInConstructorObject::build();
    }
}
