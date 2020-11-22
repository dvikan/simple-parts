<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTime;
use function sprintf;

final class FileCache implements Cache
{
    private $file;
    private $prefix;

    /** @var array */
    private $data;

    public function __construct(File $file, string $prefix = null)
    {
        $this->file = $file;
        $this->prefix = $prefix;
    }

    public function set(string $key, $value = true): void
    {
        $this->read();
        $this->data[$this->key($key)] = [
            'value' => $value,
            'created_at' => (new DateTime())->format(DATE_FORMAT),
        ];
        $this->write();
    }

    public function get(string $key, $default = null)
    {
        $this->read();
        if (! $this->has($key)) {
            return $default;
        }
        return $this->data[$this->key($key)]['value'];
    }

    public function has(string $key): bool
    {
        $this->read();
        return isset($this->data[$this->key($key)]);
    }

    public function delete(string $key): void
    {
        $this->read();
        unset($this->data[$this->key($key)]);
        $this->write();
    }

    public function clear(): void
    {
        $this->data = [];
        $this->write();
    }

    private function read()
    {
        $this->data = Json::decode($this->file->read() ?: '[]');
    }

    private function write()
    {
        $this->file->write(Json::encode($this->data));
    }

    private function key(string $key): string
    {
        if ($key === '') {
            throw new CacheException('The key cannot be the empty string');
        }

        if (isset($this->prefix)) {
            return sprintf('%s_%s', $this->prefix, $key);
        }

        return $key;
    }
}
