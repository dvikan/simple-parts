<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use ArrayAccess;

final class Container implements ArrayAccess
{
    private $container = [];
    private $resolved = [];

    public function offsetSet($offset, $fn)
    {
        $this->container[$offset] = $fn;
    }

    public function offsetGet($offset)
    {
        if (isset($this->resolved[$offset])) {
            return $this->resolved[$offset];
        }

        $resolved = $this->container[$offset]($this);

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
