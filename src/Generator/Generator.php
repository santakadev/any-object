<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

class Generator
{
    /** @var array<string, GeneratorInterface>  */
    private array $generators;

    public function __construct()
    {
        $this->generators['builder'] = new BuilderGenerator();
        $this->generators['factory'] = new FactoryGenerator();
    }

    public function generate(
        string $type,
        array $includes,
        array $excludes,
        NameResolver $nameResolver,
        OutputResolver $outputResolver = new MirrorOutputResolver(),
    ): void
    {
        if (!isset($this->generators[$type])) {
            throw new \Exception(sprintf('There\'s no generator of name "%s"', $type));
        }

        $generator = $this->generators[$type];

        $classes = (new ClassFinder())->find($includes, $excludes);
        foreach ($classes as $class) {
            $dest = $outputResolver->resolve($class);
            $generator->generate($dest->class, $outputResolver, $nameResolver);
        }
    }
}
