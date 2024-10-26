<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\Faker\Url;

class UrlCustomizedObject
{
    #[Url]
    public readonly string $value;

    public function __construct(
        #[Url]
        string $value
    ) {
        $this->value = $value;
    }
}
