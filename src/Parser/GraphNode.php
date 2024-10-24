<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Parser;

use Santakadev\AnyObject\RandomGenerator\RandomCodeGenSpec;
use Santakadev\AnyObject\RandomGenerator\RandomSpec;
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
        public RandomSpec|RandomCodeGenSpec|null $userDefinedSpec = null
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
        if (!array_key_exists($name, $this->adjacencyList)) {
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
