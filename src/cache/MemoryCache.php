<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class MemoryCache implements Cache
{
    private $memory;

    public function __construct()
    {
        $this->memory = [];
    }

    public function set($key, $value = true): void
    {
        $this->memory[$this->key($key)] = $this->prepareValue($value);
    }

    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        return $this->memory[$this->key($key)];
    }

    public function has($key): bool
    {
        return isset($this->memory[$this->key($key)]);
    }

    public function delete($key): void
    {
        unset($this->memory[$this->key($key)]);
    }

    public function clear(): void
    {
        $this->memory = [];
    }

    private function key($key): string
    {
        if ((string) $key === '') {
            throw new CacheException(
                sprintf('The key cannot evaluate to the empty string: "%s" (%s)', $key, gettype($key))
            );
        }
        return (string) $key;
    }

    private function prepareValue($value)
    {
        if ($value === null) {
            throw new CacheException('The value cannot be null');
        }
        return $value;
    }
}
