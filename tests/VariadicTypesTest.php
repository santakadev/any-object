<?php

namespace Santakadev\AnyObject\Tests;

use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfBoolObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfFloatObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfIntObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfStringObject;

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
}
