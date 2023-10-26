<?php

namespace Santakadev\AnyObject\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Santakadev\AnyObject\Parser\GraphNode;
use Santakadev\AnyObject\Parser\Parser;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\ChildObject;
use Santakadev\AnyObject\Tests\TestData\CustomTypes\ParentObject;
use Santakadev\AnyObject\Types\TClass;

class ParserTest extends TestCase
{
    public function test_constructor_circular_reference(): void
    {
        $parser = new Parser();

        $root = $parser->parseThroughConstructor(ParentObject::class);

        $parent = new GraphNode(new TClass(ParentObject::class));
        $child = new GraphNode(new TClass(ChildObject::class));
        $parent->addEdge($child, 'value');
        $child->addEdge($parent, 'value');

        $this->assertEquals(
            $parent,
            $root
        );
    }
}
