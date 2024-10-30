<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

class ClassFinder
{
    public function find(array $includes, array $excludes): array
    {
        // TODO: Remove duplication
        // This 3 lines can be cached and lazy evaluated
        // TODO: 2 value must be changed to 5 in production
        $composerJsonPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'composer.json';
        $entries = AutoloadEntries::fromComposerJson($composerJsonPath);

        $files = $this->findFiles($includes, $excludes);

        $classes = [];

        foreach ($files as $filePath) {
            $entry = $entries->findByPath($filePath);
            $offset = strlen($entry->path);
            $fileExtensionSize = strrpos($filePath, '.');
            $substr = substr($filePath, $offset, $fileExtensionSize - $offset);
            $classes[] = $entry->namespace . str_replace("/", "\\", $substr);
        }

        // TODO: Should I ensure here that it's a real class (no enum or interface)

        return $classes;
    }

    private function findFiles(array $includes, array $excludes): array
    {
        return array_diff(
            $this->getFilesByGlobs($includes),
            $this->getFilesByGlobs($excludes)
        );
    }

    private function getFilesByGlobs(array $excludes): array
    {
        $excludedFiles = [];

        foreach ($excludes as $exclude) {
            // TODO: handle duplicated
            $excludedFiles = array_merge($excludedFiles, $this->recursiveGlob($exclude));
        }

        return $excludedFiles;
    }

    private function recursiveGlob($pattern): array
    {
        // TODO: Only recurse for recursive patterns
        $files = glob($pattern);

        // TODO: Review glob flags to check if I can take advantage of them
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->recursiveGlob($dir . '/' . basename($pattern)));
        }

        return $files;
    }
}
