<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

class FixedOutputResolver implements OutputResolver
{
    public function __construct(
        private readonly string $outputDir,
        private readonly string $namespace
    ) {
    }

    public function resolve(string $class): Output
    {
        return new Output($class, $this->outputDir, $this->namespace);
    }
}
