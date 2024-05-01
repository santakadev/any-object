<?php

namespace Santakadev\AnyObject\Tests;

use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\CustomizedTypes\NumberBetweenCustomizedObject;
use Santakadev\AnyObject\Tests\TestData\CustomizedTypes\RandomDigitCustomizedObject;

class CustomizeRandomTest extends AnyObjectTestCase
{
    /** @dataProvider anyProvider */
    public function test_number_between(AnyObject $any): void
    {
        $this->assertAll(
            fn () => $any->of(NumberBetweenCustomizedObject::class)->value,
            [
                fn ($value) => $value === 5,
                fn ($value) => $value === 6,
                fn ($value) => $value === 7,
            ]
        );
    }

    /** @dataProvider anyProvider */
    public function test_random_digit(AnyObject $any): void
    {
        $this->assertAll(
            fn () => $any->of(RandomDigitCustomizedObject::class)->value,
            [
                fn ($value) => $value === 0,
                fn ($value) => $value === 1,
                fn ($value) => $value === 2,
                fn ($value) => $value === 3,
                fn ($value) => $value === 4,
                fn ($value) => $value === 5,
                fn ($value) => $value === 6,
                fn ($value) => $value === 7,
                fn ($value) => $value === 8,
                fn ($value) => $value === 9,
            ]
        );
    }
}
