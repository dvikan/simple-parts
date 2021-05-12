<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class MemoryCache implements Cache
{
    /**
     * @var array
     */
    private $cache;
    /**
     * @var SystemClock
     */
    private $clock;

    public function __construct()
    {
        $this->cache = [];
        $this->clock = new SystemClock();
    }

    public function set(string $key, $value = true, int $ttl = 0): void
    {
        $this->cache[$key] = [
            'value'             => $value,
            'ttl'               => $ttl,
            'created_at'        => $this->clock->now()->getTimestamp(),
            'created_at_human'  => $this->clock->now()->format('Y-m-d H:i:s'),
        ];
    }

    public function get(string $key, $default = null)
    {
        if (! isset($this->cache[$key])) {
            return $default;
        }

        if ($this->cache[$key]['ttl'] === 0) {
            return $this->cache[$key]['value'];
        }

        if ($this->cache[$key]['created_at'] + $this->cache[$key]['ttl'] < $this->clock->now()->getTimestamp()) {
            unset($this->cache[$key]);
            return $default;
        }

        return $this->cache[$key]['value'];
    }

    public function delete(string $key): void
    {
        if (! isset($this->cache[$key])) {
            throw new SimpleException(sprintf('Refusing to delete non-existing cache key: "%s"', $key));
        }

        unset($this->cache[$key]);
    }

    public function clear(): void
    {
        $this->cache = [];
    }
}