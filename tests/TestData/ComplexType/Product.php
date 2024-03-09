<?php

namespace Santakadev\AnyObject\Tests\TestData\ComplexType;

class Product
{
    public function __construct(
        private readonly ProductId    $id,
        private readonly ProductName  $name,
        private readonly ProductPrice $price,
    )
    {
    }

    public function id(): ProductId
    {
        return $this->id;
    }

    public function name(): ProductName
    {
        return $this->name;
    }

    public function price(): ProductPrice
    {
        return $this->price;
    }
}
