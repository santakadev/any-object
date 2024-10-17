<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Tests\TestData\CustomizedTypes;

use Santakadev\AnyObject\RandomGenerator\Uuid;

class UuidCustomizedObject
{
    #[Uuid]
    public readonly string $value;

    public function __construct(
        #[Uuid]
        string $value
    ) {
        $this->value = $value;
    }
}
