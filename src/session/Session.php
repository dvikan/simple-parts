<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Session
{
    public function __construct()
    {
        $this->start();
    }

    private function start(): void
    {
        if (session_status() === PHP_SESSION_DISABLED) {
            throw new SimpleException('Sessions are disabled');
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            throw new SimpleException('A session already exists');
        }

        if (session_status() === PHP_SESSION_NONE) {
            $options = [];
            $result = session_start($options);

            if ($result === false) {
                throw new SimpleException('Failed to start session');
            }
        }
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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new SimpleException('Tried to destroy non-active session');
        }

        if (session_destroy() === false) {
            throw new SimpleException('session_destroy() failed');
        }
    }
}