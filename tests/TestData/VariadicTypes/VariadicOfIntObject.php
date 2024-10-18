<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\VariadicTypes;

class VariadicOfIntObject
{
    /** @var int[] */
    public readonly array $value;

    public function __construct(int ...$value)
    {
        $this->value = $value;
    }
}
