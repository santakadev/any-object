<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests;

use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\CustomizedTypes\NullableCustomizedObject;
use Santakadev\AnyObject\Tests\TestData\CustomizedTypes\NumberBetweenCustomizedObject;
use Santakadev\AnyObject\Tests\TestData\CustomizedTypes\RandomDigitCustomizedObject;
use Santakadev\AnyObject\Tests\TestData\CustomizedTypes\UrlCustomizedObject;
use Santakadev\AnyObject\Tests\TestData\CustomizedTypes\UuidCustomizedObject;

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
    public function test_random_uuid(AnyObject $any): void
    {
        $value = $any->of(UuidCustomizedObject::class)->value;

        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

        $this->assertMatchesRegularExpression($pattern, $value, 'The string is not a valid UUID.');
    }

    /** @dataProvider anyProvider */
    public function test_nullable_customized_type(AnyObject $any): void
    {
        $this->assertAll(
            fn () => $any->of(NullableCustomizedObject::class)->value,
            [
                fn ($value) => $value === 5,
                fn ($value) => $value === 6,
                fn ($value) => $value === 7,
            ]
        );
    }

    /** @dataProvider anyProvider */
    public function test_random_url(AnyObject $any): void
    {
        $value = $any->of(UrlCustomizedObject::class)->value;

        $this->assertNotFalse(filter_var($value, FILTER_VALIDATE_URL));
    }
}
