<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

class Output
{
    public function __construct(
        public readonly string $dir,
        public readonly string $namespace
    ) {
    }
}
