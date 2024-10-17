<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Parser;

use Exception;
use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use ReflectionParameter;
use ReflectionProperty;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TClass;
use Santakadev\AnyObject\Types\TNull;
use Santakadev\AnyObject\Types\TScalar;
use Santakadev\AnyObject\Types\TUnion;

class PhpdocArrayParser
{
    public function parsePropertyArrayType(ReflectionProperty $reflectionProperty): TArray
    {
        $associatedDocComment = $reflectionProperty->getDocComment();
        return $this->parseArrayType($reflectionProperty, $associatedDocComment !== false ? $associatedDocComment : null);
    }

    public function parseParameterArrayType(ReflectionParameter $reflectionParameter, string $methodDocComment): TArray
    {
        return $this->parseArrayType($reflectionParameter, $methodDocComment);
    }

    private function parseArrayType(ReflectionParameter|ReflectionProperty $reflectionParameterOrProperty, ?string $associatedDocComment): TArray
    {
        $arrayPatterns = $this->docPatternsFromReflectionType($reflectionParameterOrProperty);

        if ($associatedDocComment) {
            foreach ($arrayPatterns as $arrayPattern) {
                if (preg_match($arrayPattern, $associatedDocComment, $matches) === 1) {
                    $allowsNull = $matches[1] === '?';
                    return $this->parsePhpdocArrayType($matches[2], $allowsNull, $reflectionParameterOrProperty);
                }
            }
        }

        throw new Exception(sprintf("Untyped array in %s::%s. Add type Phpdoc typed array comment.", $reflectionParameterOrProperty->getDeclaringClass()->getName(), $reflectionParameterOrProperty->getName()));
    }

    private function docPatternsFromReflectionType(ReflectionParameter|ReflectionProperty $reflectionParameterOrProperty): array
    {
        if ($reflectionParameterOrProperty instanceof ReflectionProperty) {
            return [
                '/@var\s+(\??)array<([^\s]+)>/',
                '/@var\s+(\??)([^\s]+)\[]/',
            ];
        } else {
            return [
                '/@param\s+(\??)array<([^\s]+)>/',
                '/@param\s+(\??)([^\s]+)\[]/',
            ];
        }
    }

    private function parsePhpdocArrayType($rawType, bool $allowNulls, ReflectionProperty|ReflectionParameter $reflectionPropertyOrParameter): TArray
    {
        $unionTypes = [];

        $rawTypes = explode('|', $rawType);

        foreach ($rawTypes as $typeName) {
            if (in_array($typeName, TScalar::values())) {
                $unionTypes[] = TScalar::from($typeName);
            } else if (str_starts_with($typeName, '\\')) {
                $unionTypes[] = new TClass($typeName);
            } else {
                [$namespace, $uses] = $this->buildClassNameToFQCNMap($reflectionPropertyOrParameter);
                $unionTypes[] = $uses[$typeName] ? new TClass($uses[$typeName]) : new TClass($namespace . '\\' . $typeName);
            }
        }

        if ($allowNulls) {
            $unionTypes[] = new TNull();
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

            public function enterNode(Node $node): int|Node|null
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

                return null;
            }
        };

        $traverser = new NodeTraverser();
        $traverser->addVisitor($useVisitor);
        $traverser->traverse($stmts);

        return [$useVisitor->namespace, $useVisitor->uses];
    }
}
