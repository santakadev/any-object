<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexType\Cart;

use Santakadev\AnyObject\Tests\TestData\ComplexType\Cart\Money\Currency;
use Santakadev\AnyObject\Tests\TestData\ComplexType\Cart\Money\Money;

class Cart
{
    private CartLineCollection $lines;

    public function __construct(
        private readonly CartId            $id,
        private readonly Currency          $currency,
    )
    {
        $this->lines = CartLineCollection::empty();
    }

    public function id(): CartId
    {
        return $this->id;
    }

    public function addProduct(Product $product, Quantity $quantity): void
    {
        $this->lines->add(new CartLine(CartLineId::next(), $product, $quantity));
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function total(): Money
    {
        return array_reduce(
            $this->lines->lines(),
            fn (Money $total, CartLine $line) => $total->add($line->total()),
            Money::zero($this->currency)
        );
    }
}
