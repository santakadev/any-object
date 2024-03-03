<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\Generator\StubGenerator;
use Santakadev\AnyObject\Tests\AnyObjectTestCase;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyBoolObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyFloatObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyIntObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNullableIntObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNullableStringObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyStringIntObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyStringObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\BoolObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\FloatObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\IntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableIntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\NullableStringObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringIntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class StubGeneratorTest extends AnyObjectTestCase
{
    public function test_generator_string(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(StringObject::class);
        Approvals::verifyString($text);
        $test = AnyStringObject::with(value: "test");
        $this->assertEquals("test", $test->value);
    }

    public function test_generator_int(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(IntObject::class);
        Approvals::verifyString($text);
        $test = AnyIntObject::with(1);
        $this->assertEquals(1, $test->value);
    }

    public function test_generator_float(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(FloatObject::class);
        Approvals::verifyString($text);
        $test = AnyFloatObject::with(1.1);
        $this->assertEquals(1.1, $test->value);
    }

    public function test_generator_bool(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(BoolObject::class);
        Approvals::verifyString($text);
        $test = AnyBoolObject::with(false);
        $this->assertFalse($test->value);
    }

    public function test_generator_nullable_int(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(NullableIntObject::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => (AnyNullableIntObject::with())->value,
            ['is_int', 'is_null']
        );
    }

    public function test_generator_nullable_string(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(NullableStringObject::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => (AnyNullableStringObject::with())->value,
            ['is_string', 'is_null']
        );
    }

    public function test_generator_string_int(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(StringIntObject::class);
        Approvals::verifyString($text);
        $test = AnyStringIntObject::with(str: 'str', number: 1);
        $this->assertEquals("str", $test->str);
        $this->assertEquals(1, $test->number);
        $this->assertIsString(AnyStringIntObject::build()->str);
        $this->assertIsInt(AnyStringIntObject::build()->number);
    }
}
