<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

/**
 * FileCache is a simple key-value cache with a json file as persistent storage.
 */
class FileCache implements Cache
{
    private $memory;
    private $storage;

    public function __construct(string $filePath)
    {
        $this->storage = new JsonFile($filePath);
    }

    public function has(string $key): bool
    {
        $this->load();
        return isset($this->memory[$key]);
    }

    public function get(string $key)
    {
        $this->load();

        if (! $this->has($key)) {
            throw new SimpleException(sprintf('Non-existing key "%s"', $key), SimpleException::NOT_FOUND);
        }

        return $this->memory[$key];
    }

    public function set(string $key, $value)
    {
        $this->load();
        $this->memory[$key] = $value;
        $this->write();
    }

    public function delete(string $key)
    {
        $this->load();

        if (! $this->has($key)) {
            throw new SimpleException(sprintf('Non-existing key "%s"', $key), SimpleException::NOT_FOUND);
        }

        unset($this->memory[$key]);
        $this->write();
    }

    private function load()
    {
        if(! isset($this->memory)) {
            $this->memory = $this->storage->getContents();
        }
    }

    private function write()
    {
        $this->storage->putContents($this->memory);
    }
}
