<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Config implements \ArrayAccess
{
    private $config;

    private function __construct()
    {
        // noop
    }

    public static function fromArray(array $defaultConfig, array $customConfig = []): self
    {
        if ($defaultConfig === []) {
            throw new SimpleException('Config array is empty');
        }

        foreach (array_keys($customConfig) as $key) {
            if (! isset($defaultConfig[$key])) {
                throw new SimpleException(sprintf('Illegal config key: "%s"', $key));
            }
        }

        $config = new self;
        $config->config = array_merge($defaultConfig, $customConfig);
        return $config;
    }

    public function merge(array $config): self
    {
        return self::fromArray($this->config, $config);
    }

    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->config[$offset])) {
            return $this->config[$offset];
        }

        throw new SimpleException(sprintf('Unknown config key: "%s"', $offset));
    }

    public function offsetSet($offset, $value)
    {
        throw new SimpleException('Not implemented');
    }

    public function offsetUnset($offset)
    {
        throw new SimpleException('Not implemented');
    }
}