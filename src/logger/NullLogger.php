<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class NullLogger implements Logger
{
    public function info(string $message)
    {
        // noop
    }

    public function warning(string $message)
    {
        // noop
    }

    public function error(string $message)
    {
        // noop
    }

    public function log(int $level, string $message)
    {
        // noop
    }
}
