<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

interface GeneratorInterface
{
    public function generate(string $class, OutputResolver $outputResolver, ?NameResolver $nameResolver): void;
}
