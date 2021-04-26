<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTime;
use stdClass;

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

    private $handlers;
    private $channel;

    public function __construct(string $channel, array $handlers)
    {
        $this->channel = $channel;
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
        // Special case for records that are manually tagged with an exception
        // The ErrorHandler never does this
        if (isset($context['e'])) {
            /** @var \Exception $exception */
            $exception = $context['e'];

            $context['debug'] = [
                'message'       => $exception->getMessage(),
                'code'          => $exception->getCode(),
                'file'          => $exception->getFile(),
                'line'          => $exception->getLine(),
                'stacktrace'    => $exception->getTrace(), // todo: improve stacktrace
            ];

            unset($context['e']);
        }

        // This is a user invoked log item, so tag it with debug info
        if (! isset($context['debug'])) {
            // Consider dropping this
//            $context['debug'] = [
//                'stacktrace' => debug_backtrace(), // todo: improve stacktrace
//            ];
        }

        foreach ($this->handlers as $handler) {
            $handler->handle([
                'channel'       => $this->channel,
                'level'         => $level,
                'level_name'    => self::LEVEL_NAMES[$level],
                'message'       => $message,
                'context'       => $context ?: new stdClass(),
                'datetime'      => new DateTime(),
            ]);
        }
    }
}
