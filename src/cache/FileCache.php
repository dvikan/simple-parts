<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTime;

/**
 * Stores all cached items as json
 */
final class FileCache implements Cache
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    private $file;
    private $prefix;
    private $data;

    public function __construct(File $file, string $prefix = null)
    {
        $this->file = $file;
        $this->prefix = $prefix;
    }

    public function set(string $key, $value = true): void
    {
        if (in_array(null, [$key, $value])) {
            throw new SimpleException('pls no null');
        }

        $this->read();

        $this->data[$this->key($key)] = [
            'value' => $value,
            'created_at' => (new DateTime())->format(self::DATE_FORMAT),
        ];

        $this->write();
    }

    public function get(string $key, $default = null)
    {
        if ($key === null) {
            throw new SimpleException('pls no null');
        }

        $this->read();

        if ($this->has($key)) {
            return $this->data[$this->key($key)]['value'];
        }

        if ($default === null) {
            throw new SimpleException('pls no null');
        }

        return $default;
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
        $json = $this->file->read() ?: '[]';

        $this->data = Json::decode($json);
    }

    private function write()
    {
        $json = Json::encode($this->data);

        $this->file->write($json);
    }

    private function key(string $key): string
    {
        if ($key === '') {
            throw new SimpleException('The key cannot be the empty string');
        }

        if (isset($this->prefix)) {
            return sprintf('%s_%s', $this->prefix, $key);
        }

        return $key;
    }
}
