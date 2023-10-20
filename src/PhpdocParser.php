<?php

namespace Santakadev\AnyObject;

use Exception;
use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use ReflectionParameter;
use ReflectionProperty;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TScalar;
use Santakadev\AnyObject\Types\TUnion;
use UnitEnum;

class PhpdocParser
{
    public function parsePropertyArrayType(ReflectionProperty $reflectionProperty): TArray|false
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

    public function parseParameterArrayType(ReflectionParameter $reflectionParameter, string $methodDocComment): TArray|false
    {
        $arrayPatterns = [
            '/@param\s+array<([^\s]+)>/',
            '/@param\s+([^\s]+)\[]/',
        ];

        foreach ($arrayPatterns as $arrayPattern) {
            if (preg_match($arrayPattern, $methodDocComment, $matches) === 1) {
                return $this->parsePhpdocArrayType($matches[1], $reflectionParameter);
            }
        }

        throw new Exception(sprintf("Untyped array in %s::%s. Add type Phpdoc typed array comment.", $reflectionParameter->getDeclaringClass()->getName(), $reflectionParameter->getName()));
    }

    private function parsePhpdocArrayType($rawType, ReflectionProperty|ReflectionParameter $reflectionPropertyOrParameter): TArray
    {
        $unionTypes = [];

        $rawTypes = explode('|', $rawType);

        $scalarTypes = array_map(fn (UnitEnum $e) => $e->name, TScalar::cases());
        foreach ($rawTypes as $typeName) {
            if (in_array($typeName, $scalarTypes)) {
                $unionTypes[] = TScalar::from($typeName);
            } else if (str_starts_with($typeName, '\\')) {
                $unionTypes[] = $typeName;
            } else {
                [$namespace, $uses] = $this->buildClassNameToFQCNMap($reflectionPropertyOrParameter);
                $unionTypes[] = $uses[$typeName] ?? $namespace . '\\' . $typeName;
            }
        }

        return new TArray(new TUnion($unionTypes));
    }

    private function buildClassNameToFQCNMap(ReflectionProperty|ReflectionParameter $reflectionPropertyOrParameter): array
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $stmts = $parser->parse(file_get_contents($reflectionPropertyOrParameter->getDeclaringClass()->getFileName()));

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
