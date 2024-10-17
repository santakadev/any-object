<?php

namespace Santakadev\AnyObject\Tests;

use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\VariadicTypes\VariadicOfStringObject;

class VariadicTypesTest extends AnyObjectTestCase
{
    public function test_generic_array_of_string(): void
    {
        $any = new AnyObject(useConstructor: true);
        $object = $any->of(VariadicOfStringObject::class);
        $this->assertIsArray($object->value);
        $this->assertGreaterThanOrEqual(0, count($object->value));
        $this->assertLessThanOrEqual(50, count($object->value));
        foreach ($object->value as $item) {
            $this->assertIsString($item);
        }

        // TODO: find alternative array element count assertion
        // This assertion checks if different executions produce different
        // array counts. I don't like this way of archiving this
        $this->assertAll(
            fn () => count($any->of(VariadicOfStringObject::class)->value),
            [
                fn (int $count) => $count <= 25,
                fn (int $count) => $count > 25,
            ]
        );
    }
}
