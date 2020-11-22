<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

interface Cache
{
    public function set(string $key, $value = true): void;

    public function get(string $key, $default = null);

    public function has(string $key): bool;

    public function delete(string $key): void;

    public function clear(): void;
}
