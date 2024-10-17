<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyBoolObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyFloatObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyIntObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNullableBoolObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNullableFloatObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNullableIntObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNullableStringObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyStringIntObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyStringObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\BoolObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\FloatObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\IntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableBoolObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableFloatObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableIntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableStringObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringIntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class FactoryGeneratorScalarTypesTest extends FactoryGeneratorTestCase
{
    public function test_generator_string(): void
    {
        $this->generateFactoryFor(StringObject::class);

        $text = $this->readGeneratedAnyFileFor(StringObject::class);
        Approvals::verifyString($text);
        $test = AnyStringObject::with(value: "test");
        $this->assertEquals("test", $test->value);
    }

    public function test_generator_int(): void
    {
        $this->generateFactoryFor(IntObject::class);

        $text = $this->readGeneratedAnyFileFor(IntObject::class);
        Approvals::verifyString($text);
        $test = AnyIntObject::with(1);
        $this->assertEquals(1, $test->value);
    }

    public function test_generator_float(): void
    {
        $this->generateFactoryFor(FloatObject::class);

        $text = $this->readGeneratedAnyFileFor(FloatObject::class);
        Approvals::verifyString($text);
        $test = AnyFloatObject::with(1.1);
        $this->assertEquals(1.1, $test->value);
    }

    public function test_generator_bool(): void
    {
        $this->generateFactoryFor(BoolObject::class);

        $text = $this->readGeneratedAnyFileFor(BoolObject::class);
        Approvals::verifyString($text);
        $test = AnyBoolObject::with(false);
        $this->assertFalse($test->value);
    }

    public function test_generator_nullable_string(): void
    {
        $this->generateFactoryFor(NullableStringObject::class);

        $text = $this->readGeneratedAnyFileFor(NullableStringObject::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => (AnyNullableStringObject::with())->value,
            ['is_string', 'is_null']
        );
    }

    public function test_generator_nullable_int(): void
    {
        $this->generateFactoryFor(NullableIntObject::class);

        $text = $this->readGeneratedAnyFileFor(NullableIntObject::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => (AnyNullableIntObject::with())->value,
            ['is_int', 'is_null']
        );
        $this->assertEquals(1, AnyNullableIntObject::with(1)->value);
        $this->assertNull(AnyNullableIntObject::with(null)->value);
    }

    public function test_generator_nullable_float(): void
    {
        $this->generateFactoryFor(NullableFloatObject::class);

        $text = $this->readGeneratedAnyFileFor(NullableFloatObject::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => (AnyNullableFloatObject::with())->value,
            ['is_float', 'is_null']
        );
        $this->assertEquals(1.1, AnyNullableFloatObject::with(1.1)->value);
        $this->assertNull(AnyNullableFloatObject::with(null)->value);
    }

    public function test_generator_nullable_bool(): void
    {
        $this->generateFactoryFor(NullableBoolObject::class);

        $text = $this->readGeneratedAnyFileFor(NullableBoolObject::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => (AnyNullableBoolObject::with())->value,
            ['is_bool', 'is_null']
        );
        $this->assertTrue(AnyNullableBoolObject::with(true)->value);
        $this->assertNull(AnyNullableBoolObject::with(null)->value);
    }

    public function test_generator_string_int(): void
    {
        $this->generateFactoryFor(StringIntObject::class);

        $text = $this->readGeneratedAnyFileFor(StringIntObject::class);
        Approvals::verifyString($text);
        $test = AnyStringIntObject::with(string: 'string', number: 1);
        $this->assertEquals("string", $test->string);
        $this->assertEquals(1, $test->number);
        $this->assertIsString(AnyStringIntObject::build()->string);
        $this->assertIsInt(AnyStringIntObject::build()->number);
    }
}
