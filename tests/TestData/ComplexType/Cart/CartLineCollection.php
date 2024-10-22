<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexType\Cart;

class CartLineCollection
{
    public function __construct(
        /** @var CartLine[] */
        private array $lines
    )
    {
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function add(CartLine $line): void
    {
        $this->lines[] = $line;
    }

    public function lines(): array
    {
        return $this->lines;
    }
}
