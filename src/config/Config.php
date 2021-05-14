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
        foreach (array_keys($customConfig) as $key) {
            if (! array_key_exists($key, $defaultConfig)) {
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
        return array_key_exists($offset, $this->config);
    }

    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        throw new SimpleException(sprintf('Unknown config key: "%s"', $key));
    }

    public function offsetSet($key, $value)
    {
        // possibly return a modified clone
        throw new SimpleException('Not implemented');
    }

    public function offsetUnset($offset)
    {
        throw new SimpleException('Not implemented');
    }
}