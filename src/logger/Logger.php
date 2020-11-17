<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class Logger
{
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';

    private $handlers;

    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
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

    public function log(string $severity, string $message)
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($severity, $message);
        }
    }
}
