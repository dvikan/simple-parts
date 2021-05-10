<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Config
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

    public function get(string $key)
    {
        // Consider implementing \ArrayAccess
        if (! isset($this->config[$key])) {
            throw new SimpleException(sprintf('Unknown config key: "%s"', $key));
        }

        return $this->config[$key];
    }
}