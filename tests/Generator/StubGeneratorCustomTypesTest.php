<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Generator\StubGenerator;
use Santakadev\AnyObject\Tests\AnyObjectTestCase;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyCustomObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyEnumTypeObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNullableCustomObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNullableEnumTypeObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomSubObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\EnumType;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\EnumTypeObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\NullableCustomObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\NullableEnumTypeObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class StubGeneratorCustomTypesTest extends AnyObjectTestCase
{
    public function test_generator_custom_class(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(CustomObject::class);
        Approvals::verifyString($text);
        $object = AnyCustomObject::build();
        $this->assertInstanceOf(CustomSubObject::class, $object->value);
    }

    public function test_generator_nullable_custom_class(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(NullableCustomObject::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => (AnyNullableCustomObject::with())->value,
            [
                fn ($value) => $value instanceof CustomSubObject,
                'is_null'
            ]
        );
        $this->assertEquals('string', AnyNullableCustomObject::with(new CustomSubObject(new StringObject('string')))->value->value->value);
        $this->assertNull(AnyNullableCustomObject::with(null)->value);
    }

    public function test_generator_circular_references_through_properties(): void
    {
        $this->markTestIncomplete();
    }

    public function test_generator_enum_types(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(EnumTypeObject::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => AnyEnumTypeObject::build()->enum,
            [
                fn ($value) => $value === EnumType::A,
                fn ($value) => $value === EnumType::B,
                fn ($value) => $value === EnumType::C,
            ]
        );
    }

    public function test_generator_nullable_enum_types(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(NullableEnumTypeObject::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => AnyNullableEnumTypeObject::build()->value,
            [
                fn ($value) => $value === EnumType::A,
                fn ($value) => $value === EnumType::B,
                fn ($value) => $value === EnumType::C,
                'is_null',
            ]
        );
    }
}
