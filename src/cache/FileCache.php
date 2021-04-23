<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTime;

final class FileCache implements Cache
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

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
            'created_at' => (new DateTime())->format(self::DATE_FORMAT),
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
