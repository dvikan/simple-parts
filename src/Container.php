<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use ArrayAccess;
use Exception;

class Container implements ArrayAccess
{
    private $container = [];
    private $resolved = [];

    /**
     * Value must be array or callable.
     */
    public function offsetSet($name, $value)
    {
        if (isset($this[$name])) {
            throw new Exception;
        }

        if (is_array($value)) {
            $this->resolved[$name] = $value;
        }

        $this->container[$name] = $value;
    }

    public function offsetGet($name)
    {
        if (! isset($this[$name])) {
            throw new Exception(sprintf('Dependency "%s" not found', $name));
        }

        if (isset($this->resolved[$name])) {
            return $this->resolved[$name];
        }

        $resolved = $this->container[$name]($this);

        if (! $resolved) {
            throw new Exception;
        }

        return $this->resolved[$name] = $resolved;
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
