<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class NullCache implements Cache
{
    public function has(string $key): bool
    {
        return false;
    }

    public function get(string $key)
    {
        return null;
    }

    public function set(string $key, $value)
    {
        // noop
    }

    public function delete(string $key)
    {
        // noop
    }
}
