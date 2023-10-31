<?php

namespace Santakadev\AnyObject\Parser;

use Exception;
use ReflectionClass;
use ReflectionEnum;
use ReflectionEnumPureCase;
use ReflectionEnumUnitCase;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use Santakadev\AnyObject\Types\TArray;
use Santakadev\AnyObject\Types\TClass;
use Santakadev\AnyObject\Types\TEnum;
use Santakadev\AnyObject\Types\TNull;
use Santakadev\AnyObject\Types\TScalar;
use Santakadev\AnyObject\Types\TUnion;
use function array_map;

class Parser
{
    private PhpdocArrayParser $phpdocArrayParser;

    public function __construct()
    {
        $this->phpdocArrayParser = new PhpdocArrayParser();
    }

    public function parseThroughConstructor(string $class, $visited = []): GraphNode
    {
        if (!class_exists($class)) {
            throw new Exception("Class $class does not exist");
        }

        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor(); // TODO: test an object without constructor. Fallback to properties?
        $constructorParameters = $constructor->getParameters();

        $current = new GraphNode(new TClass($class));

        $visited[$class] = $current;

        foreach ($constructorParameters as $parameter) {
            // TODO: check if there is a property with the same name and get the type from there? It could be a configuration?
            if ($parameter->isPromoted()) {
                $reflectionProperty = $reflection->getProperty($parameter->getName());
                $type = $this->typeFromReflection($reflectionProperty);
            } else {
                $type = $this->typeFromReflection($parameter, $constructor->getDocComment());
            }

            $parameterName = $parameter->getName();
            // TODO: disallow circular dependencies through constructor
            if ($type instanceof TClass) {
                if (!isset($visited[$type->class])) {
                    $current->addNamedEdge($this->parseThroughConstructor($type->class, $visited), $parameterName);
                } else {
                    $current->addNamedEdge($visited[$type->class], $parameterName);
                }
            } elseif ($type instanceof TUnion) {
                $enumNode = new GraphNode($type);
                foreach ($type->types as $type) {
                    if ($type instanceof TClass) {
                        $enumNode->addEdge($this->parseThroughConstructor($type->class, $visited));
                    } else {
                        $enumNode->addEdge(new GraphNode($type));
                    }
                }
                $current->addNamedEdge($enumNode, $parameterName);
            } elseif ($type instanceof TArray) {
                $enumNode = new GraphNode($type);
                foreach ($type->union->types as $type) {
                    if ($type instanceof TClass) {
                        $enumNode->addEdge($this->parseThroughConstructor($type->class, $visited));
                    } else {
                        $enumNode->addEdge(new GraphNode($type));
                    }
                }
                $current->addNamedEdge($enumNode, $parameterName);
            } else {
                $current->addNamedEdge(new GraphNode($type), $parameterName);
            }
        }

        return $current;
    }

    public function parseThroughProperties(string $class, $visited = []): GraphNode
    {
        if (!class_exists($class)) {
            throw new Exception("Class $class does not exist");
        }

        $current = new GraphNode(new TClass($class));
        $visited[$class] = $current;
        $reflection = new ReflectionClass($class);

        foreach ($reflection->getProperties() as $reflectionProperty) {
            $type = $this->typeFromReflection($reflectionProperty);

            $propertyName = $reflectionProperty->getName();
            if ($type instanceof TClass) {
                if (!isset($visited[$type->class])) {
                    $current->addNamedEdge($this->parseThroughProperties($type->class, $visited), $propertyName);
                } else {
                    $current->addNamedEdge($visited[$type->class], $propertyName);
                }
            } elseif ($type instanceof TUnion) {
                $enumNode = new GraphNode($type);
                foreach ($type->types as $type) {
                    if ($type instanceof TClass) {
                        $enumNode->addEdge($this->parseThroughProperties($type->class, $visited));
                    } else {
                        $enumNode->addEdge(new GraphNode($type));
                    }
                }
                $current->addNamedEdge($enumNode, $propertyName);
            } elseif ($type instanceof TArray) {
                $enumNode = new GraphNode($type);
                foreach ($type->union->types as $type) {
                    if ($type instanceof TClass) {
                        $enumNode->addEdge($this->parseThroughProperties($type->class, $visited));
                    } else {
                        $enumNode->addEdge(new GraphNode($type));
                    }
                }
                $current->addNamedEdge($enumNode, $propertyName);
            } else {
                $current->addNamedEdge(new GraphNode($type), $propertyName);
            }
        }

        return $current;
    }

    private function typeFromReflection(ReflectionParameter|ReflectionProperty $reflectionParameterOrProperty, string $methodDocComment = null): TUnion|TArray|TEnum|TScalar|TClass
    {
        $reflectionType = $reflectionParameterOrProperty->getType();

        if ($reflectionType === null) {
            throw new Exception(sprintf('Missing type declaration for property "%s"', $reflectionParameterOrProperty->getName()));
        }
        return match (get_class($reflectionType)) {
            ReflectionUnionType::class => $this->parseUnionType($reflectionType),
            ReflectionIntersectionType::class => throw new Exception(sprintf('Intersection type found in property "%s" are not supported', $reflectionParameterOrProperty->getName())),
            ReflectionNamedType::class => $this->typeFromReflectionNamedType($reflectionType, $reflectionParameterOrProperty, $methodDocComment),
        };
    }

    private function typeFromReflectionNamedType(ReflectionNamedType $reflectionType, ReflectionParameter|ReflectionProperty $reflectionParameterOrProperty, ?string $methodDocComment): TUnion|TScalar|TArray|TEnum|TClass
    {
        $typeName = $reflectionType->getName();
        if ($typeName === 'mixed') {
            throw new Exception("Unsupported type for stub creation: mixed");
        }

        if ($typeName === 'array') {
            // TODO: support of associative arrays
            if ($reflectionParameterOrProperty instanceof ReflectionProperty)
                return $this->phpdocArrayParser->parsePropertyArrayType($reflectionParameterOrProperty);
            else {
                return $this->phpdocArrayParser->parseParameterArrayType($reflectionParameterOrProperty, $methodDocComment);
            }
        }

        if (enum_exists($typeName)) {
            $reflectionEnum = new ReflectionEnum($typeName);
            $reflectionCases = $reflectionEnum->getCases();
            $cases = array_map(fn(ReflectionEnumUnitCase|ReflectionEnumPureCase $reflectionCase) => $reflectionCase->getValue(), $reflectionCases);

            if ($reflectionType->allowsNull()) {
                return new TUnion([new TEnum($cases), new TNull()]);
            } else {
                return new TEnum($cases);
            }
        }

        if (class_exists($typeName)) {
            if ($reflectionType->allowsNull()) {
                return new TUnion([new TClass($typeName), new TNull()]);
            } else {
                return new TClass($typeName);
            }
        }

        if (in_array($typeName, TScalar::values())) {
            if ($reflectionType->allowsNull()) {
                return new TUnion([TScalar::from($typeName), new TNull()]);
            } else {
                return TScalar::from($typeName);
            }
        }

        throw new Exception("Unsupported type for stub creation: $typeName");
    }

    public function parseUnionType(ReflectionUnionType $reflectionType): TUnion
    {
        $types = array_map(fn($x) => self::typeFromReflectionTypeForUnion($x), $reflectionType->getTypes());

        return new TUnion($types);
    }

    // TODO: duplicated code
    private static function typeFromReflectionTypeForUnion(ReflectionNamedType $reflectionType): TClass|TEnum|TScalar|TNull
    {
        $typeName = $reflectionType->getName();
        if ($typeName === 'mixed') {
            throw new Exception("Unsupported type for stub creation: mixed");
        }

        if (enum_exists($typeName)) {
            $reflectionEnum = new ReflectionEnum($typeName);
            $reflectionCases = $reflectionEnum->getCases();
            $cases = array_map(fn(ReflectionEnumUnitCase|ReflectionEnumPureCase $reflectionCase) => $reflectionCase->getValue(), $reflectionCases);
            return new TEnum($cases);
        }

        if (class_exists($typeName)) {
            return new TClass($typeName);
        }

        if (in_array($typeName, TScalar::values())) {
            return TScalar::from($typeName);
        }

        if ($typeName === 'null') {
            return new TNull();
        }

        if ($typeName === 'array') {
            throw new Exception("Unsupported type array in union types");
        }

        throw new Exception("Unsupported type for stub creation: $typeName");
    }
}
