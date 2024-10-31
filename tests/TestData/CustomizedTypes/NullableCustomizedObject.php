<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\Integer;

class NullableCustomizedObject
{
    #[Integer(min: 5, max: 7)]
    public readonly ?int $value;

    public function __construct(
        #[Integer(min: 5, max: 7)]
        ?int $value,
    ) {
        $this->value = $value;
    }
}
