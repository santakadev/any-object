<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\VariadicTypes;

use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;

class VariadicOfUnionTypeObject
{
    /** @var array<string|int|float|bool|null|CustomObject> */
    public readonly array $value;

    public function __construct(string|int|float|bool|null|CustomObject ...$value)
    {
        $this->value = $value;
    }
}
