<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

class FilesFinder
{
    public function find(array $includes, array $excludes): array
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
