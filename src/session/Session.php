<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Session
{
    public function __construct(array $config = [])
    {
        if (PHP_SAPI === 'cli') {
            throw new SimpleException('Sessions do not work in cli');
        }

        if (session_status() === PHP_SESSION_DISABLED) {
            throw new SimpleException('Sessions are disabled');
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            throw new SimpleException('A session already exists');
        }

        if (session_status() === PHP_SESSION_NONE) {
            $result = session_start($config);

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
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return $default;
    }

    public function destroy()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new SimpleException('Tried to destroy non-active session');
        }

        $result = session_destroy();

        if ($result === false) {
            throw new SimpleException('session_destroy() failed');
        }
    }
}