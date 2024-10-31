<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

class Generator
{
    /** @var array<string, GeneratorInterface>  */
    private array $generators = [];

    public function __construct(private readonly AutoloadEntries $autoloadEntries)
    {
    }

    public function registerGenerator(string $name, GeneratorInterface $generator): void
    {
        $this->generators[$name] = $generator;
    }

    // TODO: add defaults: name resolvers for default generators and mirror ad default resolver
    public function generate(
        string $type,
        array $includes,
        array $excludes,
        OutputResolver $outputResolver,
        ?NameResolver $nameResolver = null,
    ): void
    {
        if (!isset($this->generators[$type])) {
            throw new \Exception(sprintf('There\'s no generator of name "%s"', $type));
        }

        $generator = $this->generators[$type];

        $classes = (new ClassFinder($this->autoloadEntries))->find($includes, $excludes);
        foreach ($classes as $class) {
            $dest = $outputResolver->resolve($class);
            $generator->generate($dest->class, $outputResolver, $nameResolver);
        }
    }
}
