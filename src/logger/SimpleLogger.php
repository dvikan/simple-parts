<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class SimpleLogger implements Logger
{
    private $handlers;
    private $name;

    /**
     * @param Handler[] $handlers
     */
    public function __construct(string $name = 'default', array $handlers = [])
    {
        $this->name = $name;
        $this->handlers = $handlers;
    }

    public function info(string $message, array $context = [])
    {
        $this->log(self::INFO, $message, $context);
    }

    public function warning(string $message, array $context = [])
    {
        $this->log(self::WARNING, $message, $context);
    }

    public function error(string $message, array $context = [])
    {
        $this->log(self::ERROR, $message, $context);
    }

    public function log(int $level, string $message, array $context = [])
    {
        foreach ($this->handlers as $handler) {
            $handler->handle([
                'channel'       => $this->name,
                'level'         => $level,
                'level_name'    => self::LOG_LEVELS[$level],
                'message'       => $message,
                'context'       => $context,
                'datetime'      => new \DateTime(),
            ]);
        }
    }
}
