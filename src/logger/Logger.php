<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

interface Logger
{
    public const INFO       = 10;
    public const WARNING    = 20;
    public const ERROR      = 30;

    public const LOG_LEVELS = [
        self::INFO      => 'INFO',
        self::WARNING   => 'WARNING',
        self::ERROR     => 'ERROR',
    ];

    public function info(string $message, array $context = []): void;

    public function warning(string $message, array $context = []): void;

    public function error(string $message, array $context = []): void;

    public function log(int $level, string $message, array $context = []): void;
}
