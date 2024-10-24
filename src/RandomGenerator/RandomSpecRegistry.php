<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

class RandomSpecRegistry
{
    /** @var array<string,RandomSpec> */
    private array $specs = [];

    public function register(RandomSpec $randomObjectSpec): void
    {
        $reflectionClass = new \ReflectionClass($randomObjectSpec);

        $type = $reflectionClass->getMethod('generate')->getReturnType()->getName();

        $this->specs[$type] = $randomObjectSpec;
    }

    public function get(string $type): ?RandomSpec
    {
        if (!isset($this->specs[$type])) {
            throw new \RuntimeException(sprintf('There is no spec registered for "%s" type', $type));
        }

        return $this->specs[$type];
    }

    public function has(string $type): bool
    {
        return isset($this->specs[$type]);
    }
}
