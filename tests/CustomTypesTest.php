<?php

namespace Santakadev\AnyObject\Tests;

use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\ChildObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomSubObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\EnumType;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\EnumTypeObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\NullableCustomObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\NullableEnumTypeObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\ParentObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableStringObject;

class CustomTypesTest extends AnyObjectTestCase
{
    use AnyObjectDataProvider;

    /** @dataProvider anyProvider */
    public function test_custom_class(AnyObject $any): void
    {
        $object = $any->of(CustomObject::class);
        $this->assertInstanceOf(CustomSubObject::class, $object->value);
    }

    /** @dataProvider anyProvider */
    public function test_nullable_custom_class(AnyObject $any): void
    {
        $this->assertAll(
            fn () => $any->of(NullableCustomObject::class)->value,
            [
                fn ($value) => $value instanceof CustomSubObject,
                'is_null'
            ]
        );
    }

    /**
     * When a child object's property references an ancestor type
     * it uses the already created ancestor object.
     *
     * For now, it is not possible through constructor TODO: add a test for this
     */
    public function test_circular_references_through_properties(): void
    {
        $any = new AnyObject(useConstructor: false);
        $parent = ($any)->of(ParentObject::class);
        $child = $parent->value;
        $this->assertInstanceOf(ChildObject::class, $child);
        $this->assertEquals($parent, $child->value);
    }

    /** @dataProvider anyProvider */
    public function test_enum_types(AnyObject $any): void
    {
        $object = $any->of(EnumTypeObject::class);
        $this->assertContains($object->enum, [EnumType::A, EnumType::B, EnumType::C]);
    }

    /** @dataProvider anyProvider */
    public function test_nullable_enum_types(AnyObject $any): void
    {
        $this->assertAll(
            fn () => $any->of(NullableEnumTypeObject::class)->enum,
            [
                fn ($value) => in_array($value, [EnumType::A, EnumType::B, EnumType::C]),
                'is_null'
            ]
        );
    }
}
