<?php

namespace Santakadev\AnyObject\Tests\Generator;

use Santakadev\AnyObject\Tests\Generator\Generated\AnyCart;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyCartLine;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyCartLineCollection;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyMoney;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyProduct;
use Santakadev\AnyObject\Tests\Generator\Generated\AnyProductPrice;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Cart;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Money\Amount;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Money\Money;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Product;
use Santakadev\AnyObject\Tests\TestData\ComplexType\ProductPrice;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Quantity;

class FactoryGeneratorAcceptanceTest extends FactoryGeneratorTestCase
{
    // TODO: Support DateTimeImmutable
    // TODO: Support DateTime
    // TODO: Support DateTimeInterface
    public function test_generator_complex_type(): void
    {
        $this->generateFactoryFor(Cart::class);
        $this->generateFactoryFor(Product::class);
        $this->generateFactoryFor(Money::class);

        $quantity = new Quantity(3);
        $amount = new Amount(100);
        $price = AnyMoney::with(amount: $amount);
        $product = AnyProduct::with(price: new ProductPrice($price));
        $cart = AnyCart::with(currency: $price->currency);

        $cart->addProduct($product, $quantity);

        $this->assertEquals(300, $cart->total()->amount->value);
    }

}
