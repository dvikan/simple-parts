<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class MemoryCache implements Cache
{
    private $memory;

    public function __construct()
    {
        $this->memory = [];
    }

    public function set(string $key, $value = true): void
    {
        if ($value === null) {
            throw new SimpleException('null is not allowed as value');
        }

        $this->memory[$this->key($key)] = $value;
    }

    public function get(string $key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        return $this->memory[$this->key($key)];
    }

    public function has(string $key): bool
    {
        return isset($this->memory[$this->key($key)]);
    }

    public function delete(string $key): void
    {
        unset($this->memory[$this->key($key)]);
    }

    public function clear(): void
    {
        $this->memory = [];
    }

    private function key(string $key): string
    {
        if ($key === '') {
            throw new SimpleException('The key cannot evaluate to the empty string');
        }

        return $key;
    }
}
