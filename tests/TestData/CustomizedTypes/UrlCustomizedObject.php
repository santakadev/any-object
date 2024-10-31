<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\Faker\Faker;

class UrlCustomizedObject
{
    #[Faker("url")]
    public readonly string $value;

    public function __construct(
        #[Faker("url")]
        string $value
    ) {
        $this->value = $value;
    }
}
