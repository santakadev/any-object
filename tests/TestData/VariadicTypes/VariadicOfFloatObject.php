<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\VariadicTypes;

class VariadicOfFloatObject
{
    /** @var float[] */
    public readonly array $value;

    public function __construct(float ...$value)
    {
        $this->value = $value;
    }
}
