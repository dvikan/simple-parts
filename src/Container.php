<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use ArrayAccess;
use Closure;

class Container implements ArrayAccess
{
    private $container = [];
    private $resolved = [];

    public function offsetSet($name, $value)
    {
        if (isset($this[$name])) {
            throw new SimpleException(sprintf('Container: already has a value stored in "%s"', $name));
        }

        if ($value === null) {
            throw new SimpleException('Container: null is not allowed');
        }
        if (! $value instanceof Closure) {
            $this->resolved[$name] = $value;
        }

        $this->container[$name] = $value;
    }

    public function offsetGet($name)
    {
        if (! isset($this[$name])) {
            throw new SimpleException(sprintf('Container: dependency "%s" not found', $name));
        }

        if (isset($this->resolved[$name])) {
            return $this->resolved[$name];
        }

        return $this->resolved[$name] = $this->container[$name]($this);
    }

    public function offsetExists($name)
    {
        return isset($this->container[$name]);
    }

    public function offsetUnset($name)
    {
        unset($this->container[$name]);
        unset($this->resolved[$name]);
    }
}
