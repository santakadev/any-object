<?php

declare(strict_types=1);

namespace Santakadev\AnyObject\Types;

class TClass
{
    // TODO: Possible bug. Not all constructor calls pass the 2nd argument.
    public function __construct(
        public readonly string $class,
        public readonly string $constructor = '__construct'
    ) {
    }

    public function build(array $arguments): object
    {
        if ($this->constructor === '__construct') {
            return new $this->class(...$arguments);
        } else {
            $className = $this->class;
            $constructorName = $this->constructor;
            return call_user_func_array([$className, $constructorName], $arguments);
        }
    }
}
