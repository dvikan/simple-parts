<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

interface Cache
{
    public function has($key): bool;

    public function get($key, $default = null);

    public function set($key, $value = true);

    public function delete($key);

    public function withPrefix(string $prefix): Cache;
}
