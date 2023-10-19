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
use Santakadev\AnyObject\Tests\TestData\Constructor\NonPromotedConstructor;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\ChildObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\ParentObject;
use Santakadev\AnyObject\Tests\TestData\EnumTypes\EnumType;
use Santakadev\AnyObject\Tests\TestData\EnumTypes\EnumTypeObject;
use Santakadev\AnyObject\Tests\TestData\IntersectionTypes\IntersectionObject;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionArrayIntObject;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionBasicTypes;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionCustomTypes;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionStringIntNull;
use Santakadev\AnyObject\Tests\TestData\Untyped\MixedObject;
use Santakadev\AnyObject\Tests\TestData\Untyped\UntypedObject;

class AnyObjectTest extends TestCase
{
    /** @dataProvider anyProvider */
    public function test_string(AnyObject $any): void
    {
        $object = $any->of(StringObject::class);
        $this->assertIsString($object->value);
        $this->assertGreaterThan(0, strlen($object->value));
    }

    /** @dataProvider anyProvider */
    public function test_int(AnyObject $any): void
    {
        $object = $any->of(IntObject::class);
        $this->assertIsInt($object->value);
    }

    /** @dataProvider anyProvider */
    public function test_float(AnyObject $any): void
    {
        $object = $any->of(FloatObject::class);
        $this->assertIsFloat($object->value);
    }

    /** @dataProvider anyProvider */
    public function test_bool(AnyObject $any): void
    {
        $object = $any->of(BoolObject::class);
        $this->assertIsBool($object->value);
    }

    /** @dataProvider anyProvider */
    public function test_nullable_basic_type(AnyObject $any): void
    {
        $object = $any->of(NullableStringObject::class);
        $this->assertTrue(is_string($object->value) || is_null($object->value));
    }

    /** @dataProvider anyProvider */
    public function test_custom(AnyObject $any): void
    {
        $object = $any->of(ParentObject::class);
        $this->assertInstanceOf(ChildObject::class, $object->value);
    }

    /**
     * When a child object's property references an ancestor type
     * it uses the already created ancestor object.
     * @dataProvider anyProvider
     */
    public function test_circular_references(AnyObject $any): void
    {
        $parent = $any->of(ParentObject::class);
        $child = $parent->value;
        $this->assertInstanceOf(ChildObject::class, $child);
        $this->assertEquals($parent, $child->value);
    }

    /** @dataProvider anyProvider */
    public function test_union_basic_types(AnyObject $any): void
    {
        $object = $any->of(UnionBasicTypes::class);
        $this->assertTrue(
            is_string($object->value) ||
            is_int($object->value) ||
            is_float($object->value) ||
            is_bool($object->value)
        );
    }

    /** @dataProvider anyProvider */
    public function test_nullable_union(AnyObject $any): void
    {
        $object = $any->of(UnionStringIntNull::class);
        $this->assertTrue(
            is_string($object->value) ||
            is_int($object->value) ||
            is_null($object->value)
        );
    }

    /** @dataProvider anyProvider */
    public function test_union_custom_types(AnyObject $any): void
    {
        $object = $any->of(UnionCustomTypes::class);
        $this->assertTrue(
            $object->value instanceof StringObject ||
            $object->value instanceof IntObject
        );
    }

    // TODO: support of intersection types
    /** @dataProvider anyProvider */
    public function test_intersection_types_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Intersection type found in property "value" are not supported');
        $any->of(IntersectionObject::class);
    }

    /** @dataProvider anyProvider */
    public function test_untyped_properties_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing type declaration for property "value"');
        $any->of(UntypedObject::class);
    }

    /** @dataProvider anyProvider */
    public function test_mixed_properties_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported type for stub creation: mixed');
        $any->of(MixedObject::class);
    }

    /** @dataProvider anyProvider */
    public function test_generic_array_of_string(AnyObject $any): void
    {
        $object = $any->of(GenericArrayOfStringObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsString($item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_phpdoc_array_of_string(AnyObject $any): void
    {
        $object = $any->of(PhpdocArrayOfStringObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsString($item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_generic_array_of_int(AnyObject $any): void
    {
        $object = $any->of(GenericArrayOfIntObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsInt($item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_phpdoc_array_of_int(AnyObject $any): void
    {
        $object = $any->of(PhpdocArrayOfIntObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsInt($item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_generic_array_of_float(AnyObject $any): void
    {
        $object = $any->of(GenericArrayOfFloatObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsFloat($item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_phpdoc_array_of_float(AnyObject $any): void
    {
        $object = $any->of(PhpdocArrayOfFloatObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsFloat($item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_generic_array_of_bool(AnyObject $any): void
    {
        $object = $any->of(GenericArrayOfBoolObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsBool($item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_phpdoc_array_of_bool(AnyObject $any): void
    {
        $object = $any->of(PhpdocArrayOfBoolObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsBool($item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_generic_array_of_fqn_custom_type(AnyObject $any): void
    {
        $object = $any->of(GenericArrayOfFQNCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(ParentObject::class, $item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_phpdoc_array_of_fqn_custom_type(AnyObject $any): void
    {
        $object = $any->of(PhpdocArrayOfFQNCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(ParentObject::class, $item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_generic_array_of_use_qualified_custom_type(AnyObject $any): void
    {
        $object = $any->of(GenericArrayOfUseQualifiedCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(ParentObject::class, $item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_phpdoc_array_of_use_qualified_custom_type(AnyObject $any): void
    {
        $object = $any->of(PhpdocArrayOfUseQualifiedCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(NonQualifiedObject::class, $item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_generic_array_of_non_qualified_custom_type(AnyObject $any): void
    {
        $object = $any->of(GenericArrayOfNonQualifiedCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(NonQualifiedObject::class, $item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_phpdoc_array_of_non_qualified_custom_type(AnyObject $any): void
    {
        $object = $any->of(PhpdocArrayOfNonQualifiedCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(ParentObject::class, $item);
        }
    }

    /** @dataProvider anyProvider */
    public function test_generic_array_of_union_basic_types(AnyObject $any): void
    {
        $object = $any->of(GenericArrayOfUnionBasicTypesObject::class);
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

    /** @dataProvider anyProvider */
    public function test_enum_types(AnyObject $any): void
    {
        $object = $any->of(EnumTypeObject::class);
        $this->assertContains($object->enum, [EnumType::A, EnumType::B, EnumType::C]);
    }

    /** @dataProvider anyProvider */
    public function test_untyped_array_properties_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Untyped array in Santakadev\AnyObject\Tests\TestData\BasicTypes\ArrayObject::value. Add type Phpdoc typed array comment');
        $any->of(ArrayObject::class);
    }

    /** @dataProvider anyProvider */
    public function test_untyped_nullable_array_properties_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Untyped array in Santakadev\AnyObject\Tests\TestData\BasicTypes\NullableArrayObject::value. Add type Phpdoc typed array comment');
        $any->of(NullableArrayObject::class);
    }

    /** @dataProvider anyProvider */
    public function test_union_with_array_properties_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported type array in union types');
        $any->of(UnionArrayIntObject::class);
    }

    /** @dataProvider anyProvider */
    public function test_with_fixed_data(AnyObject $any): void
    {
        $object = $any->of(class: StringObject::class, with: ['value' => 'foo']);
        $this->assertEquals('foo', $object->value);
    }

    public function test_non_promoted_constructor_arguments(): void
    {
        $object = (new AnyObject(true))->of(NonPromotedConstructor::class);
        $this->assertTrue(is_string($object->stringProperty));
        $this->assertTrue(is_int($object->intProperty));
        $this->assertTrue(is_float($object->floatProperty));
        $this->assertTrue(is_bool($object->boolProperty));
        $this->assertTrue(is_string($object->nullableStringProperty) || is_null($object->nullableStringProperty));
        $this->assertTrue(is_int($object->nullableIntProperty) || is_null($object->nullableIntProperty));
        $this->assertTrue(is_float($object->nullableFloatProperty) || is_null($object->nullableFloatProperty));
        $this->assertTrue(is_bool($object->nullableBoolProperty) || is_null($object->nullableBoolProperty));
        $this->assertIsArray($object->arrayProperty);
        $this->assertGreaterThanOrEqual(0, count($object->arrayProperty));
        $this->assertLessThanOrEqual(50, count($object->arrayProperty));
        foreach ($object->arrayProperty as $item) {
            $this->assertIsString($item);
        }
        $this->assertTrue(
            is_string($object->unionTypeProperty) ||
            is_int($object->unionTypeProperty) ||
            is_float($object->unionTypeProperty) ||
            is_bool($object->unionTypeProperty)
        );
        // TODO: array
        // TODO: union
        // TODO: intersection
        // TODO: circular reference
        $this->assertEquals('nonAssignedProperty', $object->nonAssignedProperty);
    }

    public static function anyProvider(): array
    {
        return [
            'build from constructor' => [new AnyObject(true)],
            'build from properties' => [new AnyObject(false)],
        ];
    }
}
