<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\RandomGenerator;

use Attribute;
use Faker\Factory;
use Santakadev\AnyObject\Parser\GraphNode;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
class RandomArray implements RandomArraySpec
{
    public function __construct(
        public readonly int $minElements,
        public readonly int $maxElements,
    ) {
    }

    public function generate(GraphNode $arrayNode, callable $builder): array
    {
        $elementsCount = (Factory::create())->numberBetween($this->minElements, $this->maxElements);
        $array = [];
        for ($i = 0; $i < $elementsCount; $i++) {
            $array[] = $builder($arrayNode->pickRandomBranch());
        }
        return $array;
    }
}
