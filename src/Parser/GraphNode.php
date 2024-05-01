<?php

namespace Santakadev\AnyObject\Parser;

use Faker\Generator;
use Santakadev\AnyObject\Attributes\Number\NumberBetween;
use Santakadev\AnyObject\Attributes\Number\RandomDigit;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TClass;
use Santakadev\AnyObject\Types\TEnum;
use Santakadev\AnyObject\Types\TNull;
use Santakadev\AnyObject\Types\TScalar;
use Santakadev\AnyObject\Types\TUnion;

class GraphNode
{
    public function __construct(
        public readonly TClass|TEnum|TArray|TUnion|TScalar|TNull $type,
        /** @var array<GraphNode> */
        public array $adjacencyList = [],
        public NumberBetween|RandomDigit|null $generator = null
    ) {
    }

    public function addEdge(GraphNode $node): void
    {
        if (!in_array($node, $this->adjacencyList)) {
            $this->adjacencyList[] = $node;
        }
    }

    public function addNamedEdge(GraphNode $node, string $name): void
    {
        if (!in_array($node, $this->adjacencyList)) {
            $this->adjacencyList[$name] = $node;
        }
    }

    public function pickRandomBranch(): ?GraphNode
    {
        if (empty($this->adjacencyList)) {
            return null;
        }

        return $this->adjacencyList[array_rand($this->adjacencyList)];
    }

    public function generateInt(Generator $faker): int
    {
        return $this->generator ?
            $this->generator->generate($faker) :
            $this->defaultIntGenerator()->generate($faker);
    }

    public function defaultIntGenerator(): NumberBetween
    {
        return new NumberBetween(PHP_INT_MIN, PHP_INT_MAX);
    }
}
