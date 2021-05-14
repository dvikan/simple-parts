<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

interface Cache
{
    public function set(string $key, $value = true, int $ttl = 0): void;

    public function get(string $key, $default = null);

    public function delete(string $key): void;

    public function clear(): void;
}
