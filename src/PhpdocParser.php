<?php

namespace Santakadev\AnyObject;

use Exception;
use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use ReflectionProperty;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TUnion;

class PhpdocParser
{
    /**
     * @return string[]|false
     */
    public function parseArrayType(ReflectionProperty $reflectionProperty): TArray|false
    {
        $docblock = $reflectionProperty->getDocComment();

        $arrayPatterns = [
            '/@var\s+array<([^\s]+)>/',
            '/@var\s+([^\s]+)\[]/',
        ];

        foreach ($arrayPatterns as $arrayPattern) {
            if (preg_match($arrayPattern, $docblock, $matches) === 1) {
                return $this->parsePhpdocArrayType($matches[1], $reflectionProperty);
            }
        }

        throw new Exception(sprintf("Untyped array in %s::%s. Add type Phpdoc typed array comment.", $reflectionProperty->getDeclaringClass()->getName(), $reflectionProperty->getName()));
    }

    private function parsePhpdocArrayType($rawType, ReflectionProperty $reflectionProperty): TArray
    {
        $unionTypes = [];

        $rawTypes = explode('|', $rawType);

        $basicTypes = ['string', 'int', 'float', 'bool'];
        foreach ($rawTypes as $typeName) {
            if (in_array($typeName, $basicTypes) || str_starts_with($typeName, '\\')) {
                $unionTypes[] = $typeName;
            } else {
                [$namespace, $uses] = $this->buildClassNameToFQCNMap($reflectionProperty);
                $unionTypes[] = $uses[$typeName] ?? $namespace . '\\' . $typeName;
            }
        }

        return new TArray(new TUnion($unionTypes));
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
