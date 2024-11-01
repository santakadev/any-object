<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

// TODO: Windows compatibility
class MirrorOutputResolver implements OutputResolver
{
    public function __construct(private readonly AutoloadEntries $autoloadEntries)
    {
    }

    public function resolve(string $class): Output
    {
        // TODO: destination path should be configurable
        $destEntry = $this->autoloadEntries->findByPath('tests/');

        $reflectionClass = new \ReflectionClass($class);
        $sourceEntry = $this->autoloadEntries->findByNamespace($reflectionClass->getNamespaceName());

        $filePath = $sourceEntry->path . str_replace('\\', '/', substr($class, strlen($sourceEntry->namespace), strlen($class) - strlen($sourceEntry->namespace)));

        if (enum_exists($class)) {
            return throw new \Exception('Trying to determine the output of an enum');
        }

        if (interface_exists($class)) {
            return throw new \Exception('Trying to determine the output of an interface');
        }

        // output namespace
        $length = strrpos($filePath, "/");
        $offset = strlen($sourceEntry->path);
        $substr = substr($filePath, $offset, $length - $offset);
        $str_replace = str_replace("/", "\\", $substr);
        $outputNamespace = $destEntry->namespace . $str_replace;

        // output path
        $length = strrpos($filePath, "/");
        $offset = strlen($sourceEntry->path);
        $substr = substr($filePath, $offset, $length - $offset);
        $outputPath = $destEntry->path . $substr;

        return new Output($outputPath, $outputNamespace);
    }
}
