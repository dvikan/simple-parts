<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class FileCache implements Cache
{
    private $file;
    private $clock;
    private $isDirty;
    private $cache;

    public function __construct(File $file, Clock $clock = null)
    {
        $this->file = $file;
        $this->clock = $clock ?? new SystemClock();
        $this->isDirty = false;

        if ($file->exists()) {
            $this->cache = Json::decode($file->read() ?: '[]');
        } else {
            $this->cache = [];
            $this->write();
        }
    }

    public function set(string $key, $value = true, int $ttl = 0): void
    {
        $this->cache[$key] = [
            'value'             => $value,
            'ttl'               => $ttl,
            'created_at'        => $this->clock->now()->getTimestamp(),
        ];

        $this->isDirty = true;
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
        unset($this->cache[$key]);
        $this->isDirty = true;
    }

    public function clear(): void
    {
        $this->cache = [];
        $this->isDirty = true;
    }

    public function __destruct()
    {
        if ($this->isDirty) {
            $this->write();
        }
    }

    private function write(): void
    {
        $this->file->write(Json::encode($this->cache, JSON_PRETTY_PRINT));
    }
}
