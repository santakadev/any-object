<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\Faker\Boolean;

class BooleanCustomizedObject
{
    #[Boolean]
    public readonly bool $value;

    public function __construct(
        #[Boolean]
        bool $value
    ) {
        $this->value = $value;
    }
}
