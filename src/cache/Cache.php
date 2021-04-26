<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Cache
{
    private $file;
    private $data;

    public function __construct(TextFile $file)
    {
        $this->file = $file;

        if ($this->file->exists()) {
            $this->data = Json::decode($this->file->read());
            return;
        }
        $this->data = [];
    }

    public function set(string $key, $value = true, $ttl = null): void
    {
        $this->data[$key] = [
            'value'         => $value,
            'ttl'           => time() + $ttl,
        ];
    }

    public function get(string $key, $default = null)
    {
        if (isset($this->data[$key])) {
            // todo: implement ttl check
            return $this->data[$key]['value'];
        }

        return $default;
    }

    public function delete(string $key): void
    {
        unset($this->data[$key]);
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
