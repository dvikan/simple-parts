<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTime;

final class Logger
{
    public const INFO       = 10;
    public const WARNING    = 20;
    public const ERROR      = 30;

    public const LEVEL_NAMES = [
        self::INFO      => 'INFO',
        self::WARNING   => 'WARNING',
        self::ERROR     => 'ERROR',
    ];

    private $name;
    private $handlers;

    /**
     * @param Handler[] $handlers
     */
    public function __construct(string $name, array $handlers)
    {
        $this->name = $name;

        if ($handlers === []) {
            throw new SimpleException('Please provide at least one log handler');
        }

        $this->handlers = $handlers;
    }

    public function info(string $message, array $context = []): void
    {
        $this->log(self::INFO, $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log(self::WARNING, $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log(self::ERROR, $message, $context);
    }

    public function log(int $level, string $message, array $context = []): void
    {
        foreach ($this->handlers as $handler) {
            $handler->handle([
                'name'          => $this->name,
                'created_at'    => new DateTime(),
                'level'         => $level,
                'level_name'    => self::LEVEL_NAMES[$level],
                'message'       => $message,
                'context'       => $context,
            ]);
        }
    }
}
