<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class FileCache implements Cache
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private $file;
    private $prefix;

    /** @var array */
    private $data;

    public function __construct(File $file, string $prefix = null)
    {
        $this->file = $file;
        $this->prefix = $prefix;
    }

    public function set($key, $value = true): void
    {
        $this->read();
        $this->data[$this->key($key)] = [
            'value' => $value,
            'created_at' => (new \DateTime())->format(self::DATE_FORMAT),
        ];
        $this->write();
    }

    public function get($key, $default = null)
    {
        $this->read();
        if (! $this->has($key)) {
            return $default;
        }
        return $this->data[$this->key($key)]['value'];
    }

    public function has($key): bool
    {
        $this->read();
        return isset($this->data[$this->key($key)]);
    }

    public function delete($key): void
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
        $json = $this->file->read();

        if ($json === '') {
            $this->data = [];
            return;
        }

        $this->data = Json::decode($json);
    }

    private function write()
    {
        $this->file->write(Json::encode($this->data));
    }

    private function key($key): string
    {
        if ((string) $key === '') {
            throw new CacheException(
                sprintf('The key cannot evaluate to the empty string: "%s" (%s)', $key, gettype($key))
            );
        }
        if (isset($this->prefix)) {
            return sprintf('%s_%s', $this->prefix, $key);
        }
        return $key;
    }

    private function prepareValue($value)
    {
        if ($value === null) {
            throw new CacheException('The value cannot be null');
        }
        return $value;
    }
}
