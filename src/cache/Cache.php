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
        $this->validateKey($key);
        $this->validateValue($value);

        // todo: might be a bug here
        if ($ttl === 0) {
            $ttl = PHP_INT_MAX;
        } else {
            $ttl = (time() + $ttl);
        }

        $this->data[$key] = [
            'value' => $value,
            'ttl' => $ttl,
        ];
    }

    /**
     * Throws exception if the key do not exists, unless a default value is provided.
     */
    public function get(string $key, $default = null)
    {
        $this->validateKey($key);

        if (isset($this->data[$key])) {
            if ($this->data[$key]['ttl'] < time()) {
                $this->delete($key);
            } else {
                return $this->data[$key]['value'];
            }
        }

        if ($default === null) {
            throw new SimpleException(sprintf('Unknown cache key: "%s"', $key));
        }

        // Return the default value if it's non-null
        return $default;
    }

    public function delete(string $key): void
    {
        $this->validateKey($key);

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

    private function validateKey(string $key)
    {
        // todo: relax regex
        if (preg_match('/^[a-z]+$/i', $key) !== 1) {
            throw new SimpleException(sprintf('Illegal cache key: "%s"', $key));
        }
    }

    private function validateValue($value): void
    {
        if ($value === null) {
            throw new SimpleException('pls no null');
        }
        // perhaps only allow strings making it a key-value store
    }
}
