<?php

namespace Santakadev\AnyObject\Tests\Generator;

use ApprovalTests\Approvals;
use Santakadev\AnyObject\Tests\Generator\Generated\MoneyBuilder;
use Santakadev\AnyObject\Tests\Generator\Generated\ProductBuilder;
use Santakadev\AnyObject\Tests\Generator\Generated\ProductPriceBuilder;
use Santakadev\AnyObject\Tests\Generator\Generated\QuantityBuilder;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Money\Amount;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Product;
use Santakadev\AnyObject\Tests\TestData\ComplexType\ProductId;
use Santakadev\AnyObject\Tests\TestData\ComplexType\ProductName;
use Santakadev\AnyObject\Tests\TestData\ComplexType\ProductPrice;
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

    public function test_complex_builder(): void
    {
        $this->generateBuilderFor(Product::class);
        $text = $this->readGeneratedAnyFileFor(Product::class);
        Approvals::verifyString($text);

        $money = MoneyBuilder::create()
            ->withAmount(new Amount(5))
            ->build();
        $productPrice = new ProductPrice($money);

        $product = ProductBuilder::create()
            ->withId(new ProductId('id'))
            ->withName(new ProductName('name'))
            ->withPrice($productPrice)
            ->build();

        $this->assertEquals('id', $product->id()->value);
        $this->assertEquals('name', $product->name()->value);
        $this->assertEquals(5, $product->price()->value->amount->value);
    }
}
