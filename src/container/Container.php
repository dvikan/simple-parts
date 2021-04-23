<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use ArrayAccess;
use Closure;

final class Container implements ArrayAccess
{
    private $container = [];
    private $resolved = [];

    public function offsetSet($offset, $value)
    {
        if (isset($this[$offset])) {
            throw new SimpleException(sprintf('Already has a value stored at "%s"', $offset));
        }

        if ($value === null) {
            throw new SimpleException('null is not allowed');
        }

        if (! $value instanceof Closure) {
            $this->resolved[$offset] = $value;
        }

        $this->container[$offset] = $value;
    }

    public function offsetGet($offset)
    {
        if (! isset($this[$offset])) {
            throw new SimpleException(sprintf('Dependency "%s" not found', $offset));
        }

        if (isset($this->resolved[$offset])) {
            return $this->resolved[$offset];
        }

        $this->resolved[$offset] = $this->container[$offset]($this);

        return $this->resolved[$offset];
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
