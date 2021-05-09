<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTime;

final class Cache
{
    private $file;
    private $data;

    public function __construct(string $filePath)
    {
        $this->file = new TextFile($filePath);

        if ($this->file->exists()) {
            $this->data = Json::decode($this->file->read());
        } else {
            $this->data = [];
            $this->file->write(Json::encode($this->data));
        }
    }

    public function set(string $key, $value = true, int $ttl = 0): void
    {
        $this->data[$key] = [
            'value'             => $value,
            'ttl'               => $ttl,
            'created_at'        => $this->now()->getTimestamp(),
            'created_at_human'  => $this->now()->format('Y-m-d H:i:s'),
        ];
    }

    public function get(string $key, $default = null)
    {
        if (! isset($this->data[$key])) {
            return $default;
        }

        if ($this->data[$key]['ttl'] === 0) {
            return $this->data[$key]['value'];
        }

        if ($this->data[$key]['created_at'] + $this->data[$key]['ttl'] < $this->now()->getTimestamp()) {
            unset($this->data[$key]);
            return $default;
        }

        return $this->data[$key]['value'];
    }

    public function delete(string $key): void
    {
        unset($this->data[$key]);
    }

    public function clear(): void
    {
        $this->data = [];
    }

    protected function now(): DateTime
    {
        // todo: possibly depend on a Clock here
        return new DateTime();
    }

    public function __destruct()
    {
        $this->file->write(Json::encode($this->data));
    }
}
