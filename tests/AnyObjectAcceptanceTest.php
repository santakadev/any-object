<?php

namespace Santakadev\AnyObject\Tests;

use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\AnyObject;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Cart;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Money\Amount;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Money\Money;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Product;
use Santakadev\AnyObject\Tests\TestData\ComplexType\ProductPrice;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Quantity;

class AnyObjectAcceptanceTest extends TestCase
{
    public function test_complex_type(): void
    {
        $any = new AnyObject();

        $quantity = new Quantity(3);
        $amount = new Amount(100);
        $money = $any->of(Money::class, ['amount' => $amount]);
        $price = new ProductPrice($money);
        $product = $any->of(Product::class, ['price' => $price]);
        $cart = $any->of(Cart::class, ['currency' => $money->currency]);

        $cart->addProduct($product, $quantity);

        $this->assertEquals(300, $cart->total()->amount->value);
    }
}
