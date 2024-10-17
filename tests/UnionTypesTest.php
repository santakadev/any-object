<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests;

use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\IntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionBasicTypes;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionCustomTypes;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionStringIntNull;

class UnionTypesTest extends AnyObjectTestCase
{
    use AnyObjectDataProvider;

    /** @dataProvider anyProvider */
    public function test_union_basic_types(AnyObject $any): void
    {
        $this->assertAll(
            fn () => ($any->of(UnionBasicTypes::class))->value,
            ['is_string', 'is_int', 'is_float', 'is_bool']
        );
    }

    /** @dataProvider anyProvider */
    public function test_nullable_union(AnyObject $any): void
    {
        $this->assertAll(
            fn () => ($any->of(UnionStringIntNull::class))->value,
            ['is_string', 'is_int', 'is_null']
        );
    }

    /** @dataProvider anyProvider */
    public function test_union_custom_types(AnyObject $any): void
    {
        $this->assertAll(
            fn () => ($any->of(UnionCustomTypes::class))->value,
            assertions: [
                fn ($value) => $value instanceof StringObject,
                fn ($value) => $value instanceof IntObject
            ]
        );
    }
}
