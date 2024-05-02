<?php

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\Boolean;

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
