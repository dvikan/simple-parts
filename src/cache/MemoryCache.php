<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class MemoryCache implements Cache
{
    private $cache = [];

    public function set(string $key, $value = true, int $ttl = 0): void
    {
        if ($ttl === 0) {
            $expiration = 0;
        } else {
            $expiration = time() + $ttl;
        }

        $this->cache[$key] = [
            'value'         => $value,
            'expiration'    => $expiration,
        ];
    }

    public function get(string $key, $default = null)
    {
        if (! isset($this->cache[$key])) {
            return $default;
        }

        if ($this->cache[$key]['expiration'] === 0 || $this->cache[$key]['expiration'] >= time()) {
            return $this->cache[$key]['value'];
        }

        $this->delete($key);
        return $default;
    }

    public function delete(string $key): void
    {
        unset($this->cache[$key]);
    }

    public function clear(): void
    {
        $this->cache = [];
    }
}