<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use ArrayAccess;
use Closure;

final class Container implements ArrayAccess
{
    private $container = [];
    private $resolved = [];

    /**
     * @throws ContainerException
     */
    public function offsetSet($name, $value)
    {
        if (isset($this[$name])) {
            throw new ContainerException(sprintf('Already has a value stored at "%s"', $name));
        }

        if ($value === null) {
            throw new ContainerException('null is not allowed');
        }

        if (! $value instanceof Closure) {
            $this->resolved[$name] = $value;
        }

        $this->container[$name] = $value;
    }

    /**
     * @throws ContainerException
     */
    public function offsetGet($name)
    {
        if (! isset($this[$name])) {
            throw new ContainerException(sprintf('Dependency "%s" not found', $name));
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
