<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class NullCache implements Cache
{
    public function has($key): bool
    {
        return false;
    }

    public function get($key, $default = null)
    {
        return null;
    }

    public function set( $key, $value = true)
    {
        // noop
    }

    public function delete($key)
    {
        // noop
    }

    public function withPrefix(string $prefix): Cache
    {
        return $this;
    }
}
