<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

interface Logger
{
    public const INFO       = 10;
    public const WARNING    = 20;
    public const ERROR      = 30;

    public const LOG_LEVELS = [
        self::INFO      => 'info',
        self::WARNING   => 'warning',
        self::ERROR     => 'error',
    ];

    public function info(string $message);

    public function warning(string $message);

    public function error(string $message);

    public function log(int $level, string $message);
}
