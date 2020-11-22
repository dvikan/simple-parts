<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class NullCache implements Cache
{
    public function set(string $key, $value = true): void
    {
        // noop
    }

    public function get(string $key, $default = null)
    {
        return $default;
    }

    public function has(string $key): bool
    {
        return false;
    }

    public function delete(string $key): void
    {
        // noop
    }

    public function clear(): void
    {
        // noop
    }
}
