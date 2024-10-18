<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\VariadicTypes;

use Santakadev\AnyObject\Tests\TestData\CustomTypes\CustomObject;

class VariadicOfCustomTypeObject
{
    /** @var CustomObject[] */
    public readonly array $value;

    public function __construct(CustomObject ...$value)
    {
        $this->value = $value;
    }
}
