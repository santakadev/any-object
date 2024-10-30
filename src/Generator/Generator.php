<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

class Generator
{
    /** @var array<string, GeneratorInterface>  */
    private array $generators = [];

    /** @var array<string, OutputResolver>  */
    private array $outputResolvers = [];

    public function __construct(private readonly AutoloadEntries $autoloadEntries)
    {
    }

    public function registerGenerator(string $name, GeneratorInterface $generator): void
    {
        $this->generators[$name] = $generator;
    }

    public function registerOutputResolver(string $name, OutputResolver $outputResolver): void
    {
        $this->outputResolvers[$name] = $outputResolver;
    }

    // TODO: add defaults: name resolvers for default generators and mirror ad default resolver
    public function generate(
        string $type,
        array $includes,
        array $excludes,
        string $outputResolver,
        ?NameResolver $nameResolver = null,
    ): void
    {
        if (!isset($this->generators[$type])) {
            throw new \Exception(sprintf('There\'s no generator of name "%s"', $type));
        }

        $generator = $this->generators[$type];

        if (!isset($this->outputResolvers[$outputResolver])) {
            throw new \Exception(sprintf('There\'s no output resolver of name "%s"', $outputResolver));
        }

        $outputResolver = $this->outputResolvers[$outputResolver];

        $classes = (new ClassFinder($this->autoloadEntries))->find($includes, $excludes);
        foreach ($classes as $class) {
            $dest = $outputResolver->resolve($class);
            $generator->generate($dest->class, $outputResolver, $nameResolver);
        }
    }
}
