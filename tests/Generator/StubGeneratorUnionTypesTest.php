<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Generator\StubGenerator;
use Santakadev\AnyObject\Tests\AnyObjectTestCase;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyUnionBasicTypes;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyUnionStringIntNull;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\IntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionBasicTypes;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionCustomTypes;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionStringIntNull;

class StubGeneratorUnionTypesTest extends AnyObjectTestCase
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
    }

    public function test_union_custom_types(): void
    {
        $this->markTestIncomplete();

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
    }
}
