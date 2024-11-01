<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

final class AutoloadEntry
{
    public readonly string $path;

    public function __construct(
        public readonly string $namespace,
        string $path,
    ) {
        // TODO: review if I can avoid this mutation here
        if ($path[-1] !== '/') {
            $path .= '/';
        }

        $this->path = $path;
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
