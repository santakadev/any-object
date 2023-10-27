<?php

namespace Santakadev\AnyObject\Tests;

use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\ChildObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomSubObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\EnumType;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\EnumTypeObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\ParentObject;

class CustomTypesTest extends TestCase
{
    use AnyObjectDataProvider;

    /** @dataProvider anyProvider */
    public function test_custom_class(AnyObject $any): void
    {
        $object = $any->of(CustomObject::class);
        $this->assertInstanceOf(CustomSubObject::class, $object->value);
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
}
