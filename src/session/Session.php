<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Session
{
    public function __construct(array $options = [])
    {
        if (php_sapi_name() === 'cli') {
            throw new SimpleException('Sessions do not work in cli');
        }

        if (session_status() === PHP_SESSION_DISABLED) {
            throw new SimpleException('Sessions are disabled');
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            throw new SimpleException('A session already exists');
        }

        if (session_status() === PHP_SESSION_NONE) {
            $result = session_start($options);

            if ($result === false) {
                throw new SimpleException('Failed to start session');
            }
        }
    }

    public function set(string $key, $value = true): void
    {
        if (in_array(null, [$key, $value])) {
            throw new SimpleException('pls no null');
        }

        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        if ($key === null) {
            throw new SimpleException('pls no null');
        }

        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        if ($default === null) {
            throw new SimpleException();
        }

        return $default;
    }
}