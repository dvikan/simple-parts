<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Config implements \ArrayAccess, \JsonSerializable
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

    public function offsetExists($key)
    {
        return array_key_exists($key, $this->config);
    }

    public function offsetGet($key)
    {
        if (!array_key_exists($key, $this->config)) {
            throw new SimpleException(sprintf('Illegal config key: "%s"', $key));
        }

        return $this->config[$key];
    }

    public function offsetSet($key, $value)
    {
        if (!array_key_exists($key, $this->config)) {
            throw new SimpleException(sprintf('Illegal config key: "%s"', $key));
        }

        $this->config[$key] = $value;
    }

    public function offsetUnset($offset)
    {
        throw new SimpleException('Not implemented');
    }

    public function jsonSerialize()
    {
        return $this->config;
    }

}