<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Generator\StubGenerator;
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

class StubGeneratorScalarTypesTest extends StubGeneratorTestCase
{

    public function test_generator_string(): void
    {
        $generator = new StubGenerator();
        $generator->generate(StringObject::class, self::OUTPUT_DIR, self::OUTPUT_DIR);

        $text = $this->readGeneratedAnyFileFor(StringObject::class);
        Approvals::verifyString($text);
        $test = AnyStringObject::with(value: "test");
        $this->assertEquals("test", $test->value);
    }

    public function test_generator_int(): void
    {
        $generator = new StubGenerator();
        $generator->generate(IntObject::class, self::OUTPUT_DIR);

        $text = $this->readGeneratedAnyFileFor(IntObject::class);
        Approvals::verifyString($text);
        $test = AnyIntObject::with(1);
        $this->assertEquals(1, $test->value);
    }

    public function test_generator_float(): void
    {
        $generator = new StubGenerator();
        $generator->generate(FloatObject::class, self::OUTPUT_DIR);

        $text = $this->readGeneratedAnyFileFor(FloatObject::class);
        Approvals::verifyString($text);
        $test = AnyFloatObject::with(1.1);
        $this->assertEquals(1.1, $test->value);
    }

    public function test_generator_bool(): void
    {
        $generator = new StubGenerator();
        $generator->generate(BoolObject::class, self::OUTPUT_DIR);

        $text = $this->readGeneratedAnyFileFor(BoolObject::class);
        Approvals::verifyString($text);
        $test = AnyBoolObject::with(false);
        $this->assertFalse($test->value);
    }

    public function test_generator_nullable_string(): void
    {
        $generator = new StubGenerator();
        $generator->generate(NullableStringObject::class, self::OUTPUT_DIR);

        $text = $this->readGeneratedAnyFileFor(NullableStringObject::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => (AnyNullableStringObject::with())->value,
            ['is_string', 'is_null']
        );
    }

    public function test_generator_nullable_int(): void
    {
        $generator = new StubGenerator();
        $generator->generate(NullableIntObject::class, self::OUTPUT_DIR);

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
        $generator = new StubGenerator();
        $generator->generate(NullableFloatObject::class, self::OUTPUT_DIR);

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
        $generator = new StubGenerator();
        $generator->generate(NullableBoolObject::class, self::OUTPUT_DIR);

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
        $generator = new StubGenerator();
        $generator->generate(StringIntObject::class, self::OUTPUT_DIR);

        $text = $this->readGeneratedAnyFileFor(StringIntObject::class);
        Approvals::verifyString($text);
        $test = AnyStringIntObject::with(string: 'string', number: 1);
        $this->assertEquals("string", $test->string);
        $this->assertEquals(1, $test->number);
        $this->assertIsString(AnyStringIntObject::build()->string);
        $this->assertIsInt(AnyStringIntObject::build()->number);
    }
}
