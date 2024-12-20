<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests;

use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfBoolObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfFloatObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfFQNCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfIntObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfNonQualifiedCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfStringObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfUnionBasicTypesObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfUseQualifiedCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericNullableArrayOfStringObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\NonQualifiedObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfBoolObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfFloatObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfFQNCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfIntObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfNonQualifiedCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfStringObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocArrayOfUseQualifiedCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\PhpdocNullableArrayOfStringObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;

class ArrayTypesTest extends AnyObjectTestCase
{
    use AnyObjectDataProvider;

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
            $this->assertInstanceOf(CustomObject::class, $item);
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
            $this->assertInstanceOf(CustomObject::class, $item);
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
            $this->assertInstanceOf(CustomObject::class, $item);
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
            $this->assertInstanceOf(CustomObject::class, $item);
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
    public function test_generic_nullable_array_of_string(AnyObject $any): void
    {
        $object = $any->of(GenericNullableArrayOfStringObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            // TODO: this is not safe to catch bugs.
            $this->assertTrue(
                is_string($item) ||
                is_null($item)
            );
        }
    }

    /** @dataProvider anyProvider */
    public function test_phpdoc_nullable_array_of_string(AnyObject $any): void
    {
        $object = $any->of(PhpdocNullableArrayOfStringObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            // TODO: this is not safe to catch bugs.
            $this->assertTrue(
                is_string($item) ||
                is_null($item)
            );
        }
    }
}
