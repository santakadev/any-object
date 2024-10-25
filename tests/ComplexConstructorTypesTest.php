<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests;

use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\GuessSelfReturnNamedConstructorObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\GuessStaticReturnNamedConstructorObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\InvalidNamedConstructorObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\NamedConstructorWithoutConstructObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\NamedConstructorWithPrivateConstructObject;
use Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\NamedConstructorWithProtectedConstructObject;

class ComplexConstructorTypesTest extends AnyObjectTest
{
    public function test_build_from_named_constructor_when_constructor_is_private(): void
    {
        $any = new AnyObject();

        $object = $any->of(NamedConstructorWithPrivateConstructObject::class);

        $this->assertIsString($object->value);
    }

    public function test_build_from_named_constructor_when_constructor_is_protected(): void
    {
        $any = new AnyObject();

        $object = $any->of(NamedConstructorWithProtectedConstructObject::class);

        $this->assertIsString($object->value);
    }

    public function test_build_from_named_constructor_when_there_is_no_construct(): void
    {
        $any = new AnyObject();

        $object = $any->of(NamedConstructorWithoutConstructObject::class);

        $this->assertIsString($object->value);
    }

    public function test_guess_self_return_named_constructor(): void
    {
        $any = new AnyObject();

        $object = $any->of(GuessSelfReturnNamedConstructorObject::class);

        $this->assertIsString($object->value);
    }

    public function test_guess_static_return_named_constructor(): void
    {
        $any = new AnyObject();

        $object = $any->of(GuessStaticReturnNamedConstructorObject::class);

        $this->assertIsString($object->value);
    }

    public function test_error_when_tagging_a_non_static_named_constructor(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You have tagged a non-static method as #[NamedConstructor]. Make it static or tag the correct method: Santakadev\AnyObject\Tests\TestData\ComplexConstructorTypes\InvalidNamedConstructorObject::fromString.');

        (new AnyObject())->of(InvalidNamedConstructorObject::class);
    }

    // TODO: find public/protected constructor in parent classes
    // TODO: find named constructors in parent classes
    // TODO: use random constructor
}
