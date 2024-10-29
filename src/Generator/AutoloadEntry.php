<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

final class AutoloadEntry
{
    public function __construct(
        public readonly string $namespace,
        public readonly string $path,
    ) {
    }

    public function matchPath(string $path): bool
    {
        return str_starts_with($path, $this->path);
    }

    public function matchNamespace(string $namespace): bool
    {
        return str_starts_with($namespace, $this->namespace);
    }
}
