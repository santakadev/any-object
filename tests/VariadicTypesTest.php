<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests;

use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfBoolObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfFloatObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfIntObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfNullableCustomTypeObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfNullableString;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfStringObject;
use Santakadev\AnyObject\Tests\Utils\ArrayUtils;

class VariadicTypesTest extends AnyObjectTestCase
{
    public function test_string_variadic(): void
    {
        $any = new AnyObject(useConstructor: true);
        $object = $any->of(VariadicOfStringObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsString($item);
        }

        // TODO: find alternative array element count assertion
        // This assertion checks if different executions produce different
        // array counts. I don't like this way of archiving this
        $this->assertAll(
            fn () => count($any->of(VariadicOfStringObject::class)->value),
            [
                fn (int $count) => $count <= 25,
                fn (int $count) => $count > 25,
            ]
        );
    }

    public function test_int_variadic(): void
    {
        $any = new AnyObject(useConstructor: true);
        $object = $any->of(VariadicOfIntObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsInt($item);
        }
    }

    public function test_float_variadic(): void
    {
        $any = new AnyObject(useConstructor: true);
        $object = $any->of(VariadicOfFloatObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsFloat($item);
        }
    }

    public function test_bool_variadic(): void
    {
        $any = new AnyObject(useConstructor: true);
        $object = $any->of(VariadicOfBoolObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsBool($item);
        }
    }

    public function test_nullable_variadic(): void
    {
        $any = new AnyObject(useConstructor: true);
        $object = $any->of(VariadicOfNullableString::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));

        $match = function ($fn, array $array) {
            foreach ($array as $item) {
                if ($fn($item)) {
                    return true;
                }
            }

            return false;
        };

        $this->assertAll(
            fn () => $any->of(VariadicOfNullableString::class)->value,
            [
                fn (array $array) => ArrayUtils::array_some($array, 'is_string'),
                fn (array $array) => ArrayUtils::array_some($array, 'is_null'),
            ]
        );
    }

    public function test_custom_object_variadic(): void
    {
        $any = new AnyObject(useConstructor: true);
        $object = $any->of(VariadicOfCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertInstanceOf(CustomObject::class, $item);
        }
    }

    public function test_nullable_custom_object_variadic(): void
    {
        $any = new AnyObject(useConstructor: true);
        $object = $any->of(VariadicOfNullableCustomTypeObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        $this->assertAll(
            fn () => $any->of(VariadicOfNullableCustomTypeObject::class)->value,
            [
                fn (array $array) => ArrayUtils::array_some($array, fn ($item) => $item instanceof CustomObject),
                fn (array $array) => ArrayUtils::array_some($array, 'is_null'),
            ]
        );
    }
}
