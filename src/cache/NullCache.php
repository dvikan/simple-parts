<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class NullCache implements Cache
{
    public function set($key, $value = true): void
    {
        // noop
    }

    public function get($key, $default = null)
    {
        return $default;
    }

    public function has($key): bool
    {
        return false;
    }

    public function delete($key): void
    {
        // noop
    }

    public function clear(): void
    {
        // noop
    }
}
