<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Generator;

use Santakadev\AnyObject\Parser\GraphNode;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TClass;
use Santakadev\AnyObject\Types\TUnion;

class DFSIterator
{
    // TODO: circular references 😬
    public static function walkClass(GraphNode $node): iterable
    {
        // There's no need to traverse the rest of types to find TClass
        if (!in_array(get_class($node->type), [TClass::class, TUnion::class, TArray::class])) {
            return;
        }

        if ($node->type instanceof TClass) {
            yield $node;
        }

        foreach ($node->adjacencyList as $child) {
            yield from DFSIterator::walkClass($child);
        }
    }
}
