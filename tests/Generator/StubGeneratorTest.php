<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\Generator\StubGenerator;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyIntObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyStringIntObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyStringObject;
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
