<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\RandomInteger;

class NullableCustomizedObject
{
    #[RandomInteger(min: 5, max: 7)]
    public readonly ?int $value;

    public function __construct(
        #[RandomInteger(min: 5, max: 7)]
        ?int $value,
    ) {
        $this->value = $value;
    }
}
