<?php

namespace Santakadev\AnyObject\Tests;

use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\IntObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionBasicTypes;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionCustomTypes;
use Santakadev\AnyObject\Tests\TestData\UnionTypes\UnionStringIntNull;

class UnionTypesTest extends TestCase
{
    use AnyObjectDataProvider;

    /** @dataProvider anyProvider */
    public function test_union_basic_types(AnyObject $any): void
    {
        $object = $any->of(UnionBasicTypes::class);
        $this->assertTrue(
            is_string($object->value) ||
            is_int($object->value) ||
            is_float($object->value) ||
            is_bool($object->value)
        );
    }

    /** @dataProvider anyProvider */
    public function test_nullable_union(AnyObject $any): void
    {
        $object = $any->of(UnionStringIntNull::class);
        $this->assertTrue(
            is_string($object->value) ||
            is_int($object->value) ||
            is_null($object->value)
        );
    }

    /** @dataProvider anyProvider */
    public function test_union_custom_types(AnyObject $any): void
    {
        $object = $any->of(UnionCustomTypes::class);
        $this->assertTrue(
            $object->value instanceof StringObject ||
            $object->value instanceof IntObject
        );
    }
}
