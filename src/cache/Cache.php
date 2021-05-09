<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Cache
{
    private $file;
    /**
     * @var Clock|SystemClock
     */
    private $clock;
    private $cache;

    public function __construct(File $file, Clock $clock = null)
    {
        $this->file = $file;
        $this->clock = $clock ?? new SystemClock();
        $this->cache = [];

        if (! $this->file->exists()) {
            return;
        }

        $this->cache = Json::decode($this->file->read() ?: '[]');
    }

    public function set(string $key, $value = true, int $ttl = 0): void
    {
        $this->validate($key, $value, $ttl);

        $this->cache[$key] = [
            'value'             => $value,
            'ttl'               => $ttl,
            'created_at'        => $this->clock->now()->getTimestamp(),
            'created_at_human'  => $this->clock->now()->format('Y-m-d H:i:s'),
        ];
    }

    public function get(string $key, $default = null)
    {
        $this->validate($key, $default);

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
        $this->validate($key);

        if (! isset($this->cache[$key])) {
            throw new SimpleException(sprintf('Refusing to delete non-existing cache key: "%s"', $key));
        }

        unset($this->cache[$key]);
    }

    public function clear(): void
    {
        $this->cache = [];
    }

    private function validate(string $key, $value = true, int $ttl = 0): void
    {
        if (preg_match('#^[\w:/?=.-]{1,}$#', $key) !== 1) {
            throw new SimpleException(sprintf('Illegal cache key: "%s"', $key));
        }

        // Intentionally not validating $value

        $sevenDays = 60 * 60 * 24 * 7;

        if ($ttl < 0 || $ttl > $sevenDays) {
            throw new SimpleException(sprintf('Illegal cache ttl: %s', $ttl));
        }
    }

    public function __destruct()
    {
        $this->file->write(Json::encode($this->cache));
    }
}
