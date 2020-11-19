<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class SimpleLogger implements Logger
{
    private $handlers;
    private $name;

    /**
     * @param Handler[] $handlers
     */
    public function __construct(string $name, array $handlers = [])
    {
        $this->name = $name;
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
