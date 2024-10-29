<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

interface OutputResolver
{
    public function resolve(string $class): Output;
}
