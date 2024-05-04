<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Tests\Generator\Generated\QuantityBuilder;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Quantity;

class BuilderGeneratorTest extends BuilderGeneratorTestCase
{
    public function test_builder(): void
    {
        $this->generateBuilderFor(Quantity::class);
        $text = $this->readGeneratedAnyFileFor(Quantity::class);
        Approvals::verifyString($text);

        $quantity = QuantityBuilder::create()
            ->withValue(3)
            ->build();

        $this->assertEquals(3, $quantity->value);
    }
}
