<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class FileCache implements Cache
{
    private $file;
    private $isDirty;
    private $cache;

    public function __construct(File $file)
    {
        $this->file = $file;
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
        $this->isDirty = true;

        if ($ttl === 0) {
            $expiration = 0;
        } else {
            $expiration = time() + $ttl;
        }

        $this->cache[$key] = [
            'value'             => $value,
            'expiration'        => $expiration,
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
        $this->isDirty = true;
        unset($this->cache[$key]);
    }

    public function clear(): void
    {
        $this->isDirty = true;
        $this->cache = [];
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
