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
            throw new SimpleException(sprintf('Container value must be a closure: "%s"', $offset));
        }

        if (isset($this[$offset])) {
            throw new SimpleException(sprintf('Refusing to overwrite existing container key: "%s"', $offset));
        }

        $this->container[$offset] = $fn;
    }

    public function offsetGet($offset)
    {
        if (! isset($this[$offset])) {
            throw new SimpleException(sprintf('Unknown container key: "%s"', $offset));
        }

        if (isset($this->resolved[$offset])) {
            return $this->resolved[$offset];
        }

        $resolved = $this->container[$offset]($this);

        if (empty($resolved)) {
            throw new SimpleException(sprintf('Resolved container value was empty: "%s"', $offset));
        }

        return $this->resolved[$offset] = $resolved;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        throw new SimpleException('Not implemented');
    }
}
