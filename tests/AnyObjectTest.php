<?php

namespace Santakadev\AnyObject\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfBoolObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfNonQualifiedCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfUnionBasicTypesObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfUseQualifiedCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfFloatObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfFQNCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfIntObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfStringObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\NonQualifiedObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfBoolObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfNonQualifiedCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfUseQualifiedCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfFloatObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfFQNCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfIntObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfStringObject;
use Santakadev\AnyObject\Tests\TestData\BasicTypes\ArrayObject;
use Santakadev\AnyObject\Tests\TestData\BasicTypes\BoolObject;
use Santakadev\AnyObject\Tests\TestData\BasicTypes\FloatObject;
use Santakadev\AnyObject\Tests\TestData\BasicTypes\IntObject;
use Santakadev\AnyObject\Tests\TestData\BasicTypes\NullableArrayObject;
use Santakadev\AnyObject\Tests\TestData\BasicTypes\NullableStringObject;
use Santakadev\AnyObject\Tests\TestData\BasicTypes\StringObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\ChildObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\ParentObject;
use Santakadev\AnyObject\Tests\TestData\IntersectionTypes\IntersectionObject;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionArrayIntObject;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionBasicTypes;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionCustomTypes;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionStringIntNull;
use Santakadev\AnyObject\Tests\TestData\Untyped\MixedObject;
use Santakadev\AnyObject\Tests\TestData\Untyped\UntypedObject;

class AnyObjectTest extends TestCase
{
    private AnyObject $any;

    protected function setUp(): void
    {
        $this->any = new AnyObject();
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

    // TODO: support of intersection types
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

    public function test_generic_array_of_string(): void
    {
        $object = $this->any->of(GenericArrayOfStringObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsString($item);
        }
    }

    public function test_phpdoc_array_of_string(): void
    {
        $object = $this->any->of(PhpdocArrayOfStringObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsString($item);
        }
    }

    public function test_generic_array_of_int(): void
    {
        $object = $this->any->of(GenericArrayOfIntObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsInt($item);
        }
    }

    public function test_phpdoc_array_of_int(): void
    {
        $object = $this->any->of(PhpdocArrayOfIntObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsInt($item);
        }
    }

    public function test_generic_array_of_float(): void
    {
        $object = $this->any->of(GenericArrayOfFloatObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsFloat($item);
        }
    }

    public function test_phpdoc_array_of_float(): void
    {
        $object = $this->any->of(PhpdocArrayOfFloatObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsFloat($item);
        }
    }

    public function test_generic_array_of_bool(): void
    {
        $object = $this->any->of(GenericArrayOfBoolObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsBool($item);
        }
    }

    public function test_phpdoc_array_of_bool(): void
    {
        $object = $this->any->of(PhpdocArrayOfBoolObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsBool($item);
        }
    }

    public function test_generic_array_of_fqn_custom_type(): void
    {
        $object = $this->any->of(GenericArrayOfFQNCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(ParentObject::class, $item);
        }
    }

    public function test_phpdoc_array_of_fqn_custom_type(): void
    {
        $object = $this->any->of(PhpdocArrayOfFQNCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(ParentObject::class, $item);
        }
    }

    public function test_generic_array_of_use_qualified_custom_type(): void
    {
        $object = $this->any->of(GenericArrayOfUseQualifiedCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(ParentObject::class, $item);
        }
    }

    public function test_phpdoc_array_of_use_qualified_custom_type(): void
    {
        $object = $this->any->of(PhpdocArrayOfUseQualifiedCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(NonQualifiedObject::class, $item);
        }
    }

    public function test_generic_array_of_non_qualified_custom_type(): void
    {
        $object = $this->any->of(GenericArrayOfNonQualifiedCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(NonQualifiedObject::class, $item);
        }
    }

    public function test_phpdoc_array_of_non_qualified_custom_type(): void
    {
        $object = $this->any->of(PhpdocArrayOfNonQualifiedCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(ParentObject::class, $item);
        }
    }

    public function test_generic_array_of_union_basic_types(): void
    {
        $object = $this->any->of(GenericArrayOfUnionBasicTypesObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertTrue(
                is_string($item) ||
                is_int($item) ||
                is_float($item) ||
                is_bool($item)
            );
        }
    }

    public function test_untyped_array_properties_are_not_supported(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Untyped array in Santakadev\AnyObject\Tests\TestData\BasicTypes\ArrayObject::value. Add type Phpdoc typed array comment');
        $this->any->of(ArrayObject::class);
    }

    public function test_untyped_nullable_array_properties_are_not_supported(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Untyped array in Santakadev\AnyObject\Tests\TestData\BasicTypes\NullableArrayObject::value. Add type Phpdoc typed array comment');
        $this->any->of(NullableArrayObject::class);
    }

    public function test_union_with_array_properties_are_not_supported(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported type array in union types');
        $this->any->of(UnionArrayIntObject::class);
    }

    public function test_with_fixed_data(): void
    {
        $object = $this->any->of(class: StringObject::class, with: ['value' => 'foo']);
        $this->assertEquals('foo', $object->value);
    }
}
