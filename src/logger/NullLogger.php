<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class NullLogger implements Logger
{
    public function info(string $message, array $context = [])
    {
        // noop
    }

    public function warning(string $message, array $context = [])
    {
        // noop
    }

    public function error(string $message, array $context = [])
    {
        // noop
    }

    public function log(int $level, string $message, array $context = [])
    {
        // noop
    }
}
