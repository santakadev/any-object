<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Generator\FactoryGenerator;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyGenericArrayOfBoolObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyGenericArrayOfFloatObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyGenericArrayOfFQNCustomTypeObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyGenericArrayOfIntObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyGenericArrayOfStringObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyGenericArrayOfUnionBasicTypesObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyGenericNullableArrayOfStringObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfBoolObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfFloatObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfFQNCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfIntObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfStringObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfUnionBasicTypesObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericNullableArrayOfStringObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;

/*
 * The different ways of parsing an array are already covered the
 * AnyObject tests. So this test focuses on the generated code.
 *
 * @see Santakadev\AnyObject\Tests\ArrayTypesTest
 */
class FactoryGeneratorArrayTypesTest extends FactoryGeneratorTestCase
{
    public function test_generator_array_of_string(): void
    {
        $this->generateFactoryFor(GenericArrayOfStringObject::class);

        $text = $this->readGeneratedAnyFileFor(GenericArrayOfStringObject::class);
        Approvals::verifyString($text);
        $object = AnyGenericArrayOfStringObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsString($item);
        }
    }

    public function test_generator_array_of_int(): void
    {
        $this->generateFactoryFor(GenericArrayOfIntObject::class);

        $text = $this->readGeneratedAnyFileFor(GenericArrayOfIntObject::class);
        Approvals::verifyString($text);
        $object = AnyGenericArrayOfIntObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsInt($item);
        }
    }

    public function test_generator_array_of_float(): void
    {
        $this->generateFactoryFor(GenericArrayOfFloatObject::class);

        $text = $this->readGeneratedAnyFileFor(GenericArrayOfFloatObject::class);
        Approvals::verifyString($text);
        $object = AnyGenericArrayOfFloatObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsFloat($item);
        }
    }

    public function test_generator_array_of_bool(): void
    {
        $this->generateFactoryFor(GenericArrayOfBoolObject::class);

        $text = $this->readGeneratedAnyFileFor(GenericArrayOfBoolObject::class);
        Approvals::verifyString($text);
        $object = AnyGenericArrayOfBoolObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsBool($item);
        }
    }

    public function test_generator_array_of_fqn_custom_type(): void
    {
        $this->generateFactoryFor(GenericArrayOfFQNCustomTypeObject::class);

        $text = $this->readGeneratedAnyFileFor(GenericArrayOfFQNCustomTypeObject::class);
        Approvals::verifyString($text);
        $object = AnyGenericArrayOfFQNCustomTypeObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(CustomObject::class, $item);
        }
    }

    public function test_generator_array_of_union_basic_types(): void
    {
        $this->generateFactoryFor(GenericArrayOfUnionBasicTypesObject::class);

        $text = $this->readGeneratedAnyFileFor(GenericArrayOfUnionBasicTypesObject::class);
        Approvals::verifyString($text);
        $object = AnyGenericArrayOfUnionBasicTypesObject::build();
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

    public function test_generator_nullable_array_of_string(): void
    {
        $this->generateFactoryFor(GenericNullableArrayOfStringObject::class);

        $text = $this->readGeneratedAnyFileFor(GenericNullableArrayOfStringObject::class);
        Approvals::verifyString($text);
        $object = AnyGenericNullableArrayOfStringObject::build();
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
