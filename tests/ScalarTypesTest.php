<?php

namespace Santakadev\AnyObject\Tests;

use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\BoolObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\FloatObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\IntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableBoolObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableFloatObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableIntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableStringObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class ScalarTypesTest extends TestCase
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
        $object = $any->of(NullableStringObject::class);
        $this->assertTrue(is_string($object->value) || is_null($object->value));
    }

    /** @dataProvider anyProvider */
    public function test_nullable_int(AnyObject $any): void
    {
        $object = $any->of(NullableIntObject::class);
        $this->assertTrue(is_int($object->value) || is_null($object->value));
    }

    /** @dataProvider anyProvider */
    public function test_nullable_float(AnyObject $any): void
    {
        $object = $any->of(NullableFloatObject::class);
        $this->assertTrue(is_float($object->value) || is_null($object->value));
    }

    /** @dataProvider anyProvider */
    public function test_nullable_bool(AnyObject $any): void
    {
        $object = $any->of(NullableBoolObject::class);
        $this->assertTrue(is_bool($object->value) || is_null($object->value));
    }
}
