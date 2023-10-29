<?php

namespace Santakadev\AnyObject\Tests;

use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\BoolObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\FloatObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\IntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableBoolObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableFloatObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableIntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableStringObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class ScalarTypesTest extends AnyObjectTestCase
{
    use AnyObjectDataProvider;

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
    public function test_nullable_string(AnyObject $any): void
    {
        $this->assertAll(
            fn () => ($any->of(NullableStringObject::class))->value,
            ['is_string', 'is_null']
        );
    }

    /** @dataProvider anyProvider */
    public function test_nullable_int(AnyObject $any): void
    {
        $this->assertAll(
            fn () => ($any->of(NullableIntObject::class))->value,
            ['is_int', 'is_null']
        );
    }

    /** @dataProvider anyProvider */
    public function test_nullable_float(AnyObject $any): void
    {
        $this->assertAll(
            fn () => ($any->of(NullableFloatObject::class))->value,
            ['is_float', 'is_null']
        );
    }

    /** @dataProvider anyProvider */
    public function test_nullable_bool(AnyObject $any): void
    {
        $this->assertAll(
            fn () => ($any->of(NullableBoolObject::class))->value,
            ['is_bool', 'is_null']
        );
    }
}
