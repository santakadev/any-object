<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\Generator\StubGenerator;
use Santakadev\AnyObject\Tests\Generator\Generated\Any;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class StubGeneratorTest extends TestCase
{
    public function test_generator(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(StringObject::class);
        Approvals::verifyString($text);
        $test = Any::of(StringObject::class)->with(value: "test");
        $this->assertEquals("test", $test->value);
    }
}
