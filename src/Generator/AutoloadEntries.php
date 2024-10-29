<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

class AutoloadEntries
{
    public function __construct(
        /** @var AutoloadEntry[] */
        private array $entries = []
    ) {
    }

    public static function fromComposerJson(string $path): self
    {
        // Possible errors
        // - compose.json file not found
        // - invalid JSON
        // - JSON does not contain autoload/psr4
        // - JSON does not contain autoload-dev/psr4
        // - JSON does not contain a psr-4 value for the given directory
        // - psr4 values can be arrays or empty. see https://getcomposer.org/doc/04-schema.md#psr-4

        $composerJson = file_get_contents($path);
        $composerJson = json_decode($composerJson, true, 512, JSON_THROW_ON_ERROR);

        $autoload = array_merge(
            $composerJson['autoload']['psr-4'],
            $composerJson['autoload-dev']['psr-4']
        );

        $entries = [];

        foreach ($autoload as $namespace => $path) {
            $entries[] = new AutoloadEntry($namespace, $path);
        }


        return new self($entries);
    }

    public function findByPath(string $path): ?AutoloadEntry
    {
        // TODO: remove this hack. As match uses str_starts_with, this ensures that the longest entry will be matched
        usort($this->entries, fn (AutoloadEntry $a, AutoloadEntry$b) => strlen($b->path) - strlen($a->path));

        foreach ($this->entries as $entry) {
            if ($entry->matchPath($path)) {
                return $entry;
            }
        }

        return null;
    }

    public function findByNamespace(string $namespace): ?AutoloadEntry
    {
        // TODO: remove this hack. As match uses str_starts_with, this ensures that the longest entry will be matched
        usort($this->entries, fn (AutoloadEntry $a, AutoloadEntry$b) => strlen($b->namespace) - strlen($a->namespace));

        foreach ($this->entries as $entry) {
            if ($entry->matchNamespace($namespace)) {
                return $entry;
            }
        }

        return null;
    }
}
