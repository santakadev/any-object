<?php

declare(strict_types=1);

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

// TODO: As the parser is used from multiple places, and I'm more confident about the contracts now, I'd like to start testing in isolation
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
        $constructor = $this->findConstructor($reflection);

        $constructorParameters = $constructor->getParameters();

        $current = new GraphNode(new TClass($class, $constructor->getName(), $constructor->isVariadic()));

        $visited[$class] = $current;

        foreach ($constructorParameters as $parameter) {
            // TODO: check if there is a property with the same name and get the type from there? It could be a configuration?
            if ($parameter->isPromoted()) {
                $reflectionProperty = $reflection->getProperty($parameter->getName());
                $type = $this->typeFromReflection($reflectionProperty);
            } else {
                $methodDocComment = $constructor->getDocComment();
                $type = $this->typeFromReflection($parameter, $methodDocComment !== false ? $methodDocComment : null);
            }

            $parameterName = $parameter->getName();
            // TODO: disallow circular dependencies through constructor
            if ($type instanceof TClass) {
                if (!isset($visited[$type->class])) {
                    $node = $this->parseThroughConstructor($type->class, $visited);
                    $visited[$type->class] = $node;
                    $current->addNamedEdge($node, $parameterName);
                } else {
                    $current->addNamedEdge($visited[$type->class], $parameterName);
                }
            } elseif ($type instanceof TUnion) {
                $enumNode = new GraphNode($type);
                foreach ($type->types as $type) {
                    if ($type instanceof TClass) {
                        $node = $this->parseThroughConstructor($type->class, $visited);
                        $visited[$type->class] = $node;
                        $enumNode->addEdge($node);
                    } else {
                        $userDefinedSpec = $this->parseUserDefinedSpecAttribute($parameter);
                        $enumNode->addEdge(new GraphNode($type, [], $userDefinedSpec));
                    }
                }
                $current->addNamedEdge($enumNode, $parameterName);
            } elseif ($type instanceof TArray) {
                $enumNode = new GraphNode($type);
                foreach ($type->union->types as $type) {
                    if ($type instanceof TClass) {
                        $node = $this->parseThroughConstructor($type->class, $visited);
                        $visited[$type->class] = $node;
                        $enumNode->addEdge($node);
                    } else {
                        $enumNode->addEdge(new GraphNode($type));
                    }
                }
                $current->addNamedEdge($enumNode, $parameterName);
            } else {
                $userDefinedSpec = $this->parseUserDefinedSpecAttribute($parameter);
                $node = new GraphNode($type, [], $userDefinedSpec);
                $current->addNamedEdge($node, $parameterName);
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
                        $userDefinedSpec = $this->parseUserDefinedSpecAttribute($reflectionProperty);
                        $enumNode->addEdge(new GraphNode($type, [], $userDefinedSpec));
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
                $userDefinedSpec = $this->parseUserDefinedSpecAttribute($reflectionProperty);
                $node = new GraphNode($type, [], $userDefinedSpec);
                $current->addNamedEdge($node, $propertyName);
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
            ReflectionUnionType::class => $this->parseUnionType($reflectionType, $reflectionParameterOrProperty),
            ReflectionIntersectionType::class => throw new Exception(sprintf('Intersection type found in property "%s" are not supported', $reflectionParameterOrProperty->getName())),
            ReflectionNamedType::class => $this->typeFromReflectionNamedType($reflectionType, $reflectionParameterOrProperty, $methodDocComment),
        };
    }

    private function typeFromReflectionNamedType(ReflectionNamedType $reflectionType, ReflectionParameter|ReflectionProperty $reflectionParameterOrProperty, ?string $methodDocComment): TUnion|TScalar|TArray|TEnum|TClass
    {
        $typeName = $reflectionType->getName();

        if ($typeName === 'array') {
            // TODO: support of associative arrays
            // I dont' like that we only use the 2nd and 3rd parameter for this part of the code (arrays)
            if ($reflectionParameterOrProperty instanceof ReflectionProperty)
                return $this->phpdocArrayParser->parsePropertyArrayType($reflectionParameterOrProperty);
            else {
                return $this->phpdocArrayParser->parseParameterArrayType($reflectionParameterOrProperty, $methodDocComment);
            }
        }

        if (enum_exists($typeName)) {
            $enum = $this->buildEnumFromTypeName($typeName);
            return $reflectionType->allowsNull() ? new TUnion([$enum, new TNull()]) : $enum;
        }

        if (class_exists($typeName)) {
            if ($reflectionParameterOrProperty instanceof ReflectionParameter && $reflectionParameterOrProperty->isVariadic()) {
                $class = new TClass($typeName);
                $union = $reflectionType->allowsNull() ? new TUnion([$class, new TNull()]) : new TUnion([$class]);
                return new TArray($union);
            } else {
                $class = new TClass($typeName);
                return $reflectionType->allowsNull() ? new TUnion([$class, new TNull()]) : $class;
            }
        }

        if (in_array($typeName, TScalar::values())) {
            if ($reflectionParameterOrProperty instanceof ReflectionParameter && $reflectionParameterOrProperty->isVariadic()) {
                $scalar = TScalar::from($typeName);
                $union = $reflectionType->allowsNull() ? new TUnion([$scalar, new TNull()]) : new TUnion([$scalar]);
                return new TArray($union);
            } else {
                $scalar = TScalar::from($typeName);
                return $reflectionType->allowsNull() ? new TUnion([$scalar, new TNull()]) : $scalar;
            }
        }

        if (interface_exists($typeName)) {
            throw new Exception("Interfaces are not supported for stub creation: $typeName");
        }

        throw new Exception("Unsupported type for stub creation: $typeName");
    }

    private function parseUnionType(ReflectionUnionType $reflectionType, ReflectionParameter|ReflectionProperty $reflectionParameterOrProperty): TUnion|TArray
    {
        $types = array_map(fn($x) => $this->typeFromReflectionTypeForUnion($x), $reflectionType->getTypes());
        $union = new TUnion($types);

        if ($reflectionParameterOrProperty instanceof ReflectionParameter && $reflectionParameterOrProperty->isVariadic()) {
            return new TArray($union);
        } else {
            return $union;
        }
    }

    // TODO: duplicated code. But there are 2 differences: 1) array is not supported in union types 2) nullable types are not supported in union types
    private function typeFromReflectionTypeForUnion(ReflectionNamedType $reflectionType): TClass|TEnum|TScalar|TNull
    {
        $typeName = $reflectionType->getName();

        if (enum_exists($typeName)) {
            return $this->buildEnumFromTypeName($typeName);
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

        if (interface_exists($typeName)) {
            throw new Exception("Interfaces are not supported for stub creation: $typeName");
        }

        throw new Exception("Unsupported type for stub creation in union types: $typeName");
    }

    private function buildEnumFromTypeName(string $typeName): TEnum
    {
        $reflectionEnum = new ReflectionEnum($typeName);
        $reflectionCases = $reflectionEnum->getCases();
        $cases = array_map(fn(ReflectionEnumUnitCase|ReflectionEnumPureCase $reflectionCase) => $reflectionCase->getValue(), $reflectionCases);
        return new TEnum($cases);
    }

    public function parseUserDefinedSpecAttribute(ReflectionParameter|ReflectionProperty $parameter): ?object
    {
        $generatorAttributes = $parameter->getAttributes();

        if (count($generatorAttributes) === 0) {
            return null;
        }

        return $generatorAttributes[0]->newInstance();
    }

    private function findConstructor(ReflectionClass $reflection): ?\ReflectionMethod
    {
        $mainConstructor = $reflection->getConstructor();

        if ($mainConstructor && $mainConstructor->isPublic()) {
            return $mainConstructor;
        }

        foreach ($reflection->getMethods() as $method) {
            $attributes = $method->getAttributes(NamedConstructor::class);
            if (count($attributes) > 0) {
                if (!$method->isStatic()) {
                    throw new Exception(sprintf('You have tagged a non-static method as #[NamedConstructor]. Make it static or tag the correct method: %s::%s.', $reflection->getName(), $method->getName()));
                }

                return $method;
            }

            // Guess named constructor
            $methodReturnType = $method->getReturnType()?->getName();
            if ($method->isStatic() && ($methodReturnType === 'self' || $methodReturnType === 'static')) {
                return $method;
            }
        }

        // TODO: test an object without constructor. Fallback to properties?
        throw new \Exception(sprintf('You\'re trying to build from constructor a class with non-public constructor. Use #[NamedConstructor] to tag an alternative constructor: %s', $reflection->getName()));
    }
}
