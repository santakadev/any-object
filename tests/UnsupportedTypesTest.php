<?php

namespace Santakadev\AnyObject\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\UnsupportedTypes\InterfaceObject;
use Santakadev\AnyObject\Tests\TestData\UnsupportedTypes\IntersectionObject;
use Santakadev\AnyObject\Tests\TestData\UnsupportedTypes\MixedObject;
use Santakadev\AnyObject\Tests\TestData\UnsupportedTypes\UnionArrayIntObject;
use Santakadev\AnyObject\Tests\TestData\UnsupportedTypes\UnionInterfaceIntObject;
use Santakadev\AnyObject\Tests\TestData\UnsupportedTypes\UntypedArrayObject;
use Santakadev\AnyObject\Tests\TestData\UnsupportedTypes\UntypedNullableArrayObject;
use Santakadev\AnyObject\Tests\TestData\UnsupportedTypes\UntypedObject;

class UnsupportedTypesTest extends TestCase
{
    use AnyObjectDataProvider;

    /** @dataProvider anyProvider */
    public function test_intersection_types_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Intersection type found in property "value" are not supported');
        $any->of(IntersectionObject::class);
    }

    /** @dataProvider anyProvider */
    public function test_untyped_properties_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing type declaration for property "value"');
        $any->of(UntypedObject::class);
    }

    /** @dataProvider anyProvider */
    public function test_mixed_properties_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported type for stub creation: mixed');
        $any->of(MixedObject::class);
    }

    /** @dataProvider anyProvider */
    public function test_untyped_array_properties_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Untyped array in Santakadev\AnyObject\Tests\TestData\UnsupportedTypes\UntypedArrayObject::value. Add type Phpdoc typed array comment');
        $any->of(UntypedArrayObject::class);
    }

    /** @dataProvider anyProvider */
    public function test_untyped_nullable_array_properties_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Untyped array in Santakadev\AnyObject\Tests\TestData\UnsupportedTypes\UntypedNullableArrayObject::value. Add type Phpdoc typed array comment');
        $any->of(UntypedNullableArrayObject::class);
    }

    /** @dataProvider anyProvider */
    public function test_union_with_array_properties_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported type for stub creation in union types: array');
        $any->of(UnionArrayIntObject::class);
    }

    /** @dataProvider anyProvider */
    public function test_interfaces_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Interfaces are not supported for stub creation: Santakadev\AnyObject\Tests\TestData\UnsupportedTypes\CustomInterface');
        $any->of(InterfaceObject::class);
    }
    /** @dataProvider anyProvider */
    public function test_union_with_interfaces_are_not_supported(AnyObject $any): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Interfaces are not supported for stub creation: Santakadev\AnyObject\Tests\TestData\UnsupportedTypes\CustomInterface');
        $any->of(UnionInterfaceIntObject::class);
    }
}