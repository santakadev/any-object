<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

interface NameResolver
{
    public function resolve(string $class): string;
}
