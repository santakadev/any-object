<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\Faker\Faker;

class UuidCustomizedObject
{
    #[Faker("uuid")]
    public readonly string $value;

    public function __construct(
        #[Faker("uuid")]
        string $value
    ) {
        $this->value = $value;
    }
}
