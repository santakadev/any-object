<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyVariadicOfBoolObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyVariadicOfCustomTypeObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyVariadicOfFloatObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyVariadicOfIntObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyVariadicOfNullableStringObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyVariadicOfStringObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyVariadicOfUnionTypeObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfBoolObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfFloatObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfIntObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfNullableStringObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfStringObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfUnionTypeObject;
use Santakadev\AnyObject\Tests\Utils\ArrayUtils;

/**
 * TODO: this tests are similar to array types. Should I remove the duplication?
 */
class FactoryGeneratorVariadicTypesTest extends FactoryGeneratorTestCase
{
    public function test_generator_string_variadic(): void
    {
        $this->generateFactoryFor(VariadicOfStringObject::class);

        $text = $this->readGeneratedAnyFileFor(VariadicOfStringObject::class);
        Approvals::verifyString($text);
        $object = AnyVariadicOfStringObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsString($item);
        }
    }

    public function test_generator_int_variadic(): void
    {
        $this->generateFactoryFor(VariadicOfIntObject::class);

        $text = $this->readGeneratedAnyFileFor(VariadicOfIntObject::class);
        Approvals::verifyString($text);
        $object = AnyVariadicOfIntObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsInt($item);
        }
    }

    public function test_generator_float_variadic(): void
    {
        $this->generateFactoryFor(VariadicOfFloatObject::class);

        $text = $this->readGeneratedAnyFileFor(VariadicOfFloatObject::class);
        Approvals::verifyString($text);
        $object = AnyVariadicOfFloatObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsFloat($item);
        }
    }

    public function test_generator_bool_variadic(): void
    {
        $this->generateFactoryFor(VariadicOfBoolObject::class);

        $text = $this->readGeneratedAnyFileFor(VariadicOfBoolObject::class);
        Approvals::verifyString($text);
        $object = AnyVariadicOfBoolObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsBool($item);
        }
    }

    public function test_generator_custom_type_variadic(): void
    {
        $this->generateFactoryFor(VariadicOfCustomTypeObject::class);

        $text = $this->readGeneratedAnyFileFor(VariadicOfCustomTypeObject::class);
        Approvals::verifyString($text);
        $object = AnyVariadicOfCustomTypeObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(CustomObject::class, $item);
        }
    }

    public function test_generator_union_types_variadic(): void
    {
        $this->generateFactoryFor(VariadicOfUnionTypeObject::class);

        $text = $this->readGeneratedAnyFileFor(VariadicOfUnionTypeObject::class);
        Approvals::verifyString($text);
        $object = AnyVariadicOfUnionTypeObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        $this->assertAll(
            fn () => AnyVariadicOfUnionTypeObject::build()->value,
            [
                fn (array $array) => ArrayUtils::array_some($array, 'is_string'),
                fn (array $array) => ArrayUtils::array_some($array, 'is_int'),
                fn (array $array) => ArrayUtils::array_some($array, 'is_float'),
                fn (array $array) => ArrayUtils::array_some($array, 'is_bool'),
                fn (array $array) => ArrayUtils::array_some($array, 'is_null'),
                fn (array $array) => ArrayUtils::array_some($array, fn ($item) => $item instanceof CustomObject),
            ]
        );
    }

    public function test_generator_nullable_string_variadic(): void
    {
        $this->generateFactoryFor(VariadicOfNullableStringObject::class);

        $text = $this->readGeneratedAnyFileFor(VariadicOfNullableStringObject::class);
        Approvals::verifyString($text);
        $object = AnyVariadicOfNullableStringObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        $this->assertAll(
            fn () => AnyVariadicOfNullableStringObject::build()->value,
            [
                fn (array $array) => ArrayUtils::array_some($array, 'is_string'),
                fn (array $array) => ArrayUtils::array_some($array, 'is_null'),
            ]
        );
    }
}
