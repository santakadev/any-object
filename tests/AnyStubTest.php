<?php

namespace Santakadev\AnyStub\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Santakadev\AnyStub\AnyStub;
use Santakadev\AnyStub\Tests\TestData\BasicTypes\ArrayObject;
use Santakadev\AnyStub\Tests\TestData\BasicTypes\BoolObject;
use Santakadev\AnyStub\Tests\TestData\BasicTypes\FloatObject;
use Santakadev\AnyStub\Tests\TestData\BasicTypes\IntObject;
use Santakadev\AnyStub\Tests\TestData\BasicTypes\NullableArrayObject;
use Santakadev\AnyStub\Tests\TestData\BasicTypes\NullableStringObject;
use Santakadev\AnyStub\Tests\TestData\BasicTypes\StringObject;
use Santakadev\AnyStub\Tests\TestData\CustomTypes\ChildObject;
use Santakadev\AnyStub\Tests\TestData\CustomTypes\ParentObject;
use Santakadev\AnyStub\Tests\TestData\IntersectionTypes\IntersectionObject;
use Santakadev\AnyStub\Tests\TestData\UnionTypes\UnionArrayIntObject;
use Santakadev\AnyStub\Tests\TestData\UnionTypes\UnionBasicTypes;
use Santakadev\AnyStub\Tests\TestData\UnionTypes\UnionCustomTypes;
use Santakadev\AnyStub\Tests\TestData\UnionTypes\UnionStringIntNull;
use Santakadev\AnyStub\Tests\TestData\Untyped\MixedObject;
use Santakadev\AnyStub\Tests\TestData\Untyped\UntypedObject;

class AnyStubTest extends TestCase
{
    private AnyStub $any;

    protected function setUp(): void
    {
        $this->any = new AnyStub();
    }

    public function test_string(): void
    {
        $object = $this->any->of(StringObject::class);
        $this->assertIsString($object->value);
        $this->assertGreaterThan(0, strlen($object->value));
    }

    public function test_int(): void
    {
        $object = $this->any->of(IntObject::class);
        $this->assertIsInt($object->value);
    }

    public function test_float(): void
    {
        $object = $this->any->of(FloatObject::class);
        $this->assertIsFloat($object->value);
    }

    public function test_bool(): void
    {
        $object = $this->any->of(BoolObject::class);
        $this->assertIsBool($object->value);
    }

    public function test_nullable_basic_type(): void
    {
        $object = $this->any->of(NullableStringObject::class);
        $this->assertTrue(is_string($object->value) || is_null($object->value));
    }

    public function test_custom(): void
    {
        $object = $this->any->of(ParentObject::class);
        $this->assertInstanceOf(ChildObject::class, $object->value);
    }

    /**
     * When a child object's property references a ancestor type
     * it uses the already created ancestor object.
     */
    public function test_circular_references(): void
    {
        $parent = $this->any->of(ParentObject::class);
        $child = $parent->value;
        $this->assertInstanceOf(ChildObject::class, $child);
        $this->assertEquals($parent, $child->value);
    }

    public function test_union_basic_types(): void
    {
        $object = $this->any->of(UnionBasicTypes::class);
        $this->assertTrue(
            is_string($object->value) ||
            is_int($object->value) ||
            is_float($object->value) ||
            is_bool($object->value)
        );
    }

    public function test_nullable_union(): void
    {
        $object = $this->any->of(UnionStringIntNull::class);
        $this->assertTrue(
            is_string($object->value) ||
            is_int($object->value) ||
            is_null($object->value)
        );
    }

    public function test_union_custom_types(): void
    {
        $object = $this->any->of(UnionCustomTypes::class);
        $this->assertTrue(
            $object->value instanceof StringObject ||
            $object->value instanceof IntObject
        );
    }

    public function test_intersection_types_are_not_supported(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Intersection type found in property "value" are not supported');
        $this->any->of(IntersectionObject::class);
    }

    public function test_untyped_properties_are_not_supported(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing type declaration for property "value"');
        $this->any->of(UntypedObject::class);
    }

    public function test_mixed_properties_are_not_supported(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported type for stub creation: mixed');
        $this->any->of(MixedObject::class);
    }

    public function test_array_properties_are_not_supported(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported type for stub creation: array');
        $this->any->of(ArrayObject::class);
    }

    public function test_nullable_array_properties_are_not_supported(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported type for stub creation: array');
        $this->any->of(NullableArrayObject::class);
    }

    public function test_union_with_array_properties_are_not_supported(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported type for stub creation: array');
        $this->any->of(UnionArrayIntObject::class);
    }
}
