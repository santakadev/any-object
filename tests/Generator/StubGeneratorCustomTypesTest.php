<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Generator\StubGenerator;
use Santakadev\AnyObject\Tests\AnyObjectTestCase;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyCustomObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyCustomSubObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNullableCustomObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomSubObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\NullableCustomObject;
use Santakadev\AnyObject\Tests\TestData\ScalarTypes\StringObject;

class StubGeneratorCustomTypesTest extends AnyObjectTestCase
{
    public function test_custom_class(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(CustomObject::class);
        Approvals::verifyString($text);
        $object = AnyCustomObject::build();
        $this->assertInstanceOf(CustomSubObject::class, $object->value);
    }

    public function test_nullable_custom_class(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(NullableCustomObject::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => (AnyNullableCustomObject::with())->value,
            [
                fn ($value) => $value instanceof CustomSubObject,
                'is_null'
            ]
        );
        $this->assertEquals('string', AnyNullableCustomObject::with(new CustomSubObject(new StringObject('string')))->value->value->value);
        $this->assertNull(AnyNullableCustomObject::with(null)->value);
    }
}
