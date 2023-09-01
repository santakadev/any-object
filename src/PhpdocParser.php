<?php

namespace Santakadev\AnyObject;

use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use ReflectionProperty;

class PhpdocParser
{
    public function parseArrayType(ReflectionProperty $reflectionProperty): string|false
    {
        $docblock = $reflectionProperty->getDocComment();

        $arrayPatterns = [
            '/@var\s+array<([^\s]+)>/',
            '/@var\s+([^\s]+)\[]/',
        ];

        // TODO: support for union types like array<int|string>
        foreach ($arrayPatterns as $arrayPattern) {
            if (preg_match($arrayPattern, $docblock, $matches) === 1 ) {
                return $this->parsePhpdocArrayType($matches[1], $reflectionProperty);
            }
        }

        return false;
    }

    private function parsePhpdocArrayType($rawType, ReflectionProperty $reflectionProperty): string
    {
        $basicTypes = ['string', 'int', 'float', 'bool'];
        if (!in_array($rawType, $basicTypes)) {
            if (!str_starts_with($rawType, '\\')) {
                [$namespace, $uses] = $this->buildClassNameToFQCNMap($reflectionProperty);
                $rawType = $uses[$rawType] ?? $namespace . '\\' . $rawType;
            }
        }
        return $rawType;
    }

    private function buildClassNameToFQCNMap(ReflectionProperty $reflectionProperty): array
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $stmts = $parser->parse(file_get_contents($reflectionProperty->getDeclaringClass()->getFileName()));

        $useVisitor = new class extends NodeVisitorAbstract {
            public array $uses = [];
            public string $namespace;

            public function enterNode(Node $node)
            {
                if ($node instanceof Use_) {
                    foreach ($node->uses as $use) {
                        $useValue = $use->name->toString();
                        $useValueParts = explode('\\', $useValue);
                        $className = end($useValueParts);
                        $this->uses[$className] = $useValue;
                    }
                }

                if ($node instanceof Node\Stmt\Namespace_) {
                    $this->namespace = $node->name->toString();
                }
            }
        };

        $traverser = new NodeTraverser();
        $traverser->addVisitor($useVisitor);
        $traverser->traverse($stmts);


        return [$useVisitor->namespace, $useVisitor->uses];
    }
}
