<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Config
{
    private $config;

    public function __construct(array $default, array $custom)
    {
        $this->config = array_merge($default, $custom);

        if (count($this->config) !== count($custom)) {
            throw new SimpleException('Found illegal config key');
        }
    }

    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}