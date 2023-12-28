<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\Generator\StubGenerator;
use Santakadev\AnyObject\Tests\Generator\Generated\Any;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\IntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringIntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class StubGeneratorTest extends TestCase
{
    public function test_generator_string(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(StringObject::class);
        Approvals::verifyString($text);
        $test = Any::of(StringObject::class)->with(value: "test");
        $this->assertEquals("test", $test->value);
    }

    public function test_generator_int(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(IntObject::class);
        Approvals::verifyString($text);
        $test = Any::of(IntObject::class)->with(1);
        $this->assertEquals(1, $test->value);
    }

    public function test_generator_string_int(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(StringIntObject::class);
        Approvals::verifyString($text);
        $test = Any::of(StringIntObject::class)->with("str", 1);
        $this->assertEquals("str", $test->str);
        $this->assertEquals(1, $test->number);
        $this->assertIsString(Any::of(StringIntObject::class)->build()->str);
        $this->assertIsInt(Any::of(StringIntObject::class)->build()->number);
    }
}
