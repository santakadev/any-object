<?php

namespace Santakadev\AnyObject\Tests\TestData\ComplexType;

use Santakadev\AnyObject\Tests\TestData\ComplexType\Money\Money;

class CartLine
{
    public function __construct(
        private readonly CartLineId $id,
        private readonly Product    $product,
        private readonly Quantity   $quantity,
    )
    {
    }

    public function id(): CartLineId
    {
        return $this->id;
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function quantity(): Quantity
    {
        return $this->quantity;
    }

    public function total(): Money
    {
        return $this->product->price()->multiply($this->quantity);
    }
}
