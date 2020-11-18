<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class Logger
{
    public const INFO = 10;
    public const WARNING = 20;
    public const ERROR = 30;

    public const LOG_LEVELS = [
        self::INFO => 'INFO',
        self::WARNING => 'WARNING',
        self::ERROR => 'ERROR',
    ];

    private $handlers;
    private $name;

    public function __construct(string $name = 'default', array $handlers = [])
    {
        $this->handlers = $handlers;
        $this->name = $name;
    }

    public function info(string $message)
    {
        $this->log(self::INFO, $message);
    }

    public function warning(string $message)
    {
        $this->log(self::WARNING, $message);
    }

    public function error(string $message)
    {
        $this->log(self::ERROR, $message);
    }

    public function log(int $level, string $message)
    {
        foreach ($this->handlers as $handler) {
            $handler->handle([
                'channel'       => $this->name,
                'level'         => $level,
                'level_name'    => self::LOG_LEVELS[$level],
                'message'       => $message,
                'datetime'      => new \DateTime(),
            ]);
        }
    }
}
