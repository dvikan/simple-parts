<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

interface Cache
{
    public function set($key, $value = true): void;

    public function get($key, $default = null);

    public function has($key): bool;

    public function delete($key): void;

    public function clear(): void;
}
