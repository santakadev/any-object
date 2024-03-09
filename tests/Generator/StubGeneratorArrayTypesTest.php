<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Generator\StubGenerator;
use Santakadev\AnyObject\Tests\AnyObjectTestCase;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyGenericArrayOfBoolObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyGenericArrayOfFloatObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyGenericArrayOfIntObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyGenericArrayOfStringObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfBoolObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfFloatObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfIntObject;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfStringObject;

/*
 * The different ways of parsing an array are already covered the
 * AnyObject tests. So this test focuses on the generated code.
 *
 * @see Santakadev\AnyObject\Tests\ArrayTypesTest
 */
class StubGeneratorArrayTypesTest extends AnyObjectTestCase
{
    public function test_generator_array_of_string(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(GenericArrayOfStringObject::class);
        Approvals::verifyString($text);
        $object = AnyGenericArrayOfStringObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsString($item);
        }
    }

    public function test_generator_array_of_int(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(GenericArrayOfIntObject::class);
        Approvals::verifyString($text);
        $object = AnyGenericArrayOfIntObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsInt($item);
        }
    }

    public function test_generator_array_of_float(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(GenericArrayOfFloatObject::class);
        Approvals::verifyString($text);
        $object = AnyGenericArrayOfFloatObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsFloat($item);
        }
    }

    public function test_generator_array_of_bool(): void
    {
        $generator = new StubGenerator();
        $text = $generator->generate(GenericArrayOfBoolObject::class);
        Approvals::verifyString($text);
        $object = AnyGenericArrayOfBoolObject::build();
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsBool($item);
        }
    }
}
