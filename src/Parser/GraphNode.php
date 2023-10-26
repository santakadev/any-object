<?php

namespace Santakadev\AnyObject\Parser;

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
        public array $adjacencyList = []
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
}
