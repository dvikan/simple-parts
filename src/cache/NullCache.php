<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class NullCache implements Cache
{
    public function set(string $key, $value = true, int $ttl = 0): void
    {
        // noop
    }

    public function get(string $_, $default = null)
    {
        return $default;
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