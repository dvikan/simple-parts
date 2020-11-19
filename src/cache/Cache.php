<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

interface Cache
{
    public function has(string $key): bool;

    public function get(string $key);

    public function set(string $key, $value);

    public function delete(string $key);

    public function withPrefix(string $prefix): Cache;
}
