<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

/**
 * FileCache is a simple key-value cache with a json file as persistent storage.
 */
class FileCache implements Cache
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private $storage;
    private $prefix;
    private $memory;

    public function __construct(string $filePath, string $prefix = null)
    {
        $this->storage = new JsonFile($filePath);
        $this->prefix = $prefix;
    }

    public function withPrefix(string $prefix): Cache
    {
        $fileCache = clone $this;
        $fileCache->prefix = $prefix;

        return $fileCache;
    }

    public function get($key, $default = null)
    {
        $this->load();
        if (! $this->has($key)) {
            return $default;
        }
        return $this->memory[$this->key($key)]['value'];
    }

    public function has($key): bool
    {
        $this->load();
        return isset($this->memory[$this->key($key)]);
    }

    public function set($key, $value = true)
    {
        $this->load();
        $this->memory[$this->key($key)] = [
            'value' => $value,
            'created_at' => now()->format(self::DATE_FORMAT),
        ];
        $this->write();
    }

    public function delete($key)
    {
        $this->load();
        unset($this->memory[$this->key($key)]);
        $this->write();
    }

    public function clear()
    {
        $this->memory = [];
        $this->write();
    }

    private function load()
    {
        $this->memory = $this->storage->getContents();
    }

    private function write()
    {
        $this->storage->putContents($this->memory);
    }

    private function key($key)
    {
        if (isset($this->prefix)) {
            return sprintf('%s_%s', $this->prefix, $key);
        }
        return $key;
    }
}
