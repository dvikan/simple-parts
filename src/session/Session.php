<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Session
{
    public function __construct()
    {
        session_start();
    }

    public function set(string $key, $value = true): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function destroy()
    {
        session_destroy();
    }
}