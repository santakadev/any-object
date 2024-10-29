<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

class WrapNameResolver implements NameResolver
{
    public function __construct(
        private readonly string $prefix,
        private readonly string $suffix,
    ) {
    }

    public function resolve(string $class): string
    {
        return $this->prefix . $class . $this->suffix;
    }
}
