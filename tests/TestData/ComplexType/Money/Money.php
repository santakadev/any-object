<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\ComplexType\Money;

use InvalidArgumentException;

class Money
{
    public function __construct(
        public readonly Amount $amount,
        public readonly Currency $currency
    ) {
    }

    public static function zero(Currency $currency): Money
    {
        return new self(
            new Amount(0),
            $currency
        );
    }

    public function multiply(int $amount): Money
    {
        return new self(
            new Amount($this->amount->value * $amount),
            $this->currency
        );
    }

    public function add(Money $total): Money
    {
        if ($this->currency->isoCode !== $total->currency->isoCode) {
            throw new InvalidArgumentException('Currencies must be the same');
        }

        return new self(
            new Amount($this->amount->value + $total->amount->value),
            $this->currency
        );
    }
}
