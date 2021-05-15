<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Container implements \ArrayAccess
{
    private $values = [];
    private $resolved = [];

    public function offsetSet($key, $value)
    {
        $this->values[$key] = $value;
    }

    public function offsetGet($key)
    {
        if (isset($this->resolved[$key])) {
            return $this->resolved[$key];
        }

        if (!isset($this[$key])) {
            throw new SimpleException(sprintf('Unknown container key: "%s"', $key));
        }

        $this->resolved[$key] = $this->values[$key]($this);

        return $this->resolved[$key];
    }

    public function offsetExists($key): bool
    {
        return isset($this->values[$key]);
    }

    public function offsetUnset($key)
    {
        throw new SimpleException('Not implemented');
    }
}
