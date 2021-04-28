<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Cache
{
    private $file;
    private $data;

    public function __construct(TextFile $file)
    {
        $this->file = $file;

        if (! $this->file->exists()) {
            $this->data = [];
            return;
        }

        $this->data = Json::decode($this->file->read());
    }

    public function set(string $key, $value = true, int $ttl = 0): void
    {
        $this->data[$key] = [
            'key'           => $key,
            'value'         => $value,
            'created_at'    => time(),
            'ttl'           => $ttl,
        ];
    }

    public function get(string $key, $default = null)
    {
        if ($key === '') {
            throw new SimpleException('Cache key cannot be null');
        }

        if (! isset($this->data[$key])) {
            return $default;
        }

        if ($this->data[$key]['ttl'] === 0) {
            return $this->data[$key]['value'];
        }

        if ($this->data[$key]['created_at'] + $this->data[$key]['ttl'] < time()) {
            unset($this->data[$key]);
            return $default;
        }

        return $this->data[$key]['value'];
    }

    public function delete(string $key): void
    {
        if (! isset($this->data[$key])) {
            throw new SimpleException(sprintf('Unknown cache key: "%s"', $key));
        }
    }

    public function clear(): void
    {
        $this->data = [];
    }

    public function __destruct()
    {
        $this->file->write(Json::encode($this->data));
    }
}
