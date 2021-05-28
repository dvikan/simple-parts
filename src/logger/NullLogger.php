<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class NullLogger implements Logger
{

    public function info(string $message, array $context = []): void
    {
        // noop
    }

    public function warning(string $message, array $context = []): void
    {
        // noop
    }

    public function error(string $message, array $context = []): void
    {
        // noop
    }

    public function log(int $level, string $message, array $context = []): void
    {
        // noop
    }
}