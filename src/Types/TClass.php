<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Types;

class TClass
{
    // TODO: Possible bug. Not all constructor calls pass the 2nd argument.
    public function __construct(
        public readonly string $class,
        public readonly string $constructor = '__construct',
        public readonly bool $isVariadic = false
    ) {
    }

    public function build(array $arguments): object
    {
        if ($this->constructor === '__construct') {
            if ($this->isVariadic) {
                // This flattens the last argument
                $array_merge = array_merge([...array_slice($arguments, 0, -1)], end($arguments));
                return new $this->class(...$array_merge);
            } else {
                return new $this->class(...$arguments);
            }
        } else {
            $className = $this->class;
            $constructorName = $this->constructor;
            return call_user_func_array([$className, $constructorName], $arguments);
        }
    }
}
