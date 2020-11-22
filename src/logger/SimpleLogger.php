<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTime;

class SimpleLogger implements Logger
{
    private $handlers;
    private $name;

    /**
     * @param Handler[] $handlers
     */
    public function __construct(string $name = 'default', array $handlers = null)
    {
        $this->name = $name;
        $this->handlers = $handlers ?? [new PrintHandler()];
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
                'channel'       => $this->name,
                'level'         => $level,
                'level_name'    => self::LOG_LEVELS[$level],
                'message'       => $message,
                'context'       => $context,
                'datetime'      => new DateTime(),
            ]);
        }
    }
}