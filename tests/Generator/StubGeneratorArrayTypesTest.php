<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Generator\StubGenerator;
use Santakadev\AnyObject\Tests\AnyObjectTestCase;
use Santakadev\AnyObject\Tests\TestData\ArrayTypes\GenericArrayOfStringObject;

class StubGeneratorArrayTypesTest extends AnyObjectTestCase
{
    public function test_generator_generic_array_of_string(): void
    {
        $this->markTestIncomplete();
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
}
