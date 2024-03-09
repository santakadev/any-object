<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Generator\StubGenerator;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyUnionBasicTypes;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyUnionCustomTypes;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyUnionStringIntNull;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\IntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionBasicTypes;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionCustomTypes;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionStringIntNull;

class StubGeneratorUnionTypesTest extends StubGeneratorTestCase
{
    public function test_generator_union_basic_types(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(UnionBasicTypes::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => ((AnyUnionBasicTypes::with()))->value,
            ['is_string', 'is_int', 'is_float', 'is_bool']
        );
        $this->assertEquals('string', AnyUnionBasicTypes::with('string')->value);
        $this->assertEquals(1, AnyUnionBasicTypes::with(1)->value);
        $this->assertEquals(1.1, AnyUnionBasicTypes::with(1.1)->value);
        $this->assertTrue(AnyUnionBasicTypes::with(true)->value);
    }

    public function test_generator_nullable_union(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(UnionStringIntNull::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => (AnyUnionStringIntNull::with())->value,
            ['is_string', 'is_int', 'is_null']
        );
        $this->assertEquals('string', AnyUnionStringIntNull::with('string')->value);
        $this->assertEquals(1, AnyUnionStringIntNull::with(1)->value);
        $this->assertNull(AnyUnionStringIntNull::with(null)->value);
    }

    public function test_generator_union_custom_types(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(UnionCustomTypes::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => (AnyUnionCustomTypes::with())->value,
            assertions: [
                fn ($value) => $value instanceof StringObject,
                fn ($value) => $value instanceof IntObject
            ]
        );
        $this->assertEquals(1, AnyUnionCustomTypes::with(new IntObject(1))->value->value);
        $this->assertEquals('string', AnyUnionCustomTypes::with(new StringObject('string'))->value->value);
    }
}
