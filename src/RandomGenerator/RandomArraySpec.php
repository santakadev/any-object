<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use Santakadev\AnyObject\Parser\GraphNode;

interface RandomArraySpec
{
    public function generate(GraphNode $arrayNode, callable $builder): array;
}
