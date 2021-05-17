<?php
declare(strict_types=1);

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

    public function offsetExists($key)
    {
        return isset($this->config[$key]);
    }

    public function offsetGet($key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        throw new SimpleException(sprintf('Unknown config key: "%s"', $key));
    }

    public function offsetSet($key, $value)
    {
        throw new SimpleException('Not implemented');
    }

    public function offsetUnset($offset)
    {
        throw new SimpleException('Not implemented');
    }
}