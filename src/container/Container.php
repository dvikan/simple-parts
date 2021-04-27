<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use ArrayAccess;
use Closure;

final class Container implements ArrayAccess
{
    private $container = [];
    private $resolved = [];

    public function offsetSet($offset, $fn)
    {
        if (! $fn instanceof Closure) {
            throw new SimpleException('Container value must be a closure');
        }

        if (isset($this[$offset])) {
            throw new SimpleException(sprintf('Refusing to overwrote existing key: "%s"', $offset));
        }

        $this->container[$offset] = $fn;
    }

    public function offsetGet($offset)
    {
        if (! isset($this[$offset])) {
            throw new SimpleException(sprintf('Key does not exists: "%s"', $offset));
        }

        if (isset($this->resolved[$offset])) {
            return $this->resolved[$offset];
        }

        return $this->resolved[$offset] = $this->container[$offset]($this); // Intentional assignment
    }

    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
        unset($this->resolved[$offset]);
    }
}
