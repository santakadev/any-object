<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexType;

use DateTime;

class Product
{
    public function __construct(
        private readonly ProductId $id,
        private readonly ProductName $name,
        private readonly ProductPrice $price,
        private readonly DateTime $createdAt
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

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }
}
