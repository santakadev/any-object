<?php

namespace Santakadev\AnyObject\RandomGenerator;

use Santakadev\AnyObject\Parser\GraphNode;

interface RandomArraySpec
{
    public function generate(GraphNode $arrayNode, callable $builder): array;
}
