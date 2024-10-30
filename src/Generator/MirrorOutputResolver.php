<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

// TODO: Windows compatibility
// TODO: simplify code
class MirrorOutputResolver implements OutputResolver
{
    public function __construct(private readonly AutoloadEntries $autoloadEntries)
    {
    }

    public function resolve(string $class): Output
    {
        $destEntry = $this->autoloadEntries->findByPath('tests/');

        $reflectionClass = new \ReflectionClass($class);
        $sourceEntry = $this->autoloadEntries->findByNamespace($reflectionClass->getNamespaceName());

        $filePath = $sourceEntry->path . str_replace('\\', '/', substr($class, strlen($sourceEntry->namespace), strlen($class) - strlen($sourceEntry->namespace)));

        // ignore enums and interfaces
        if (enum_exists($class) || interface_exists($class)) {
            return throw new \Exception('Trying to determine the output of a enum or interface');
        }

        // namespace
        $length = strrpos($filePath, "/");
        $offset = strlen($sourceEntry->path);
        $substr = substr($filePath, $offset, $length - $offset);
        $str_replace = str_replace("/", "\\", $substr);
        $outputNamespace = $destEntry->namespace . $str_replace;

        // output
        $length = strrpos($filePath, "/");
        $offset = strlen($sourceEntry->path);
        $substr = substr($filePath, $offset, $length - $offset);
        $output = $destEntry->path . $substr;

        return new Output($class, $output, $outputNamespace);
    }
}
