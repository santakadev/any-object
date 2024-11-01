<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyNumberBetweenCustomizedObject;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyRandomDigitCustomizedObject;
use Santakadev\AnyObject\Tests\TestData\CustomizedTypes\NumberBetweenCustomizedObject;
use Santakadev\AnyObject\Tests\TestData\CustomizedTypes\RandomDigitCustomizedObject;

class FactoryGeneratorCustomizedTypesTest extends FactoryGeneratorTestCase
{
    public function test_customized_int_with_number_between(): void
    {
        $this->generateFactoryFor(NumberBetweenCustomizedObject::class);

        $text = $this->readGeneratedAnyFileFor(NumberBetweenCustomizedObject::class);
        Approvals::verifyString($text);
        $this->assertAll(
            fn () => AnyNumberBetweenCustomizedObject::build()->value,
            [
                fn ($value) => $value === 5,
                fn ($value) => $value === 6,
                fn ($value) => $value === 7,
            ]
        );
    }
}
