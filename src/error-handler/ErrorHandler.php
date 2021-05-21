<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class ErrorHandler
{
    private const LEVEL_MAP = [
        E_ERROR             => Logger::ERROR,
        E_WARNING           => Logger::WARNING,
        E_PARSE             => Logger::ERROR,
        E_NOTICE            => Logger::INFO,
        E_CORE_ERROR        => Logger::ERROR,
        E_CORE_WARNING      => Logger::WARNING,
        E_COMPILE_ERROR     => Logger::ERROR,
        E_COMPILE_WARNING   => Logger::WARNING,
        E_USER_ERROR        => Logger::ERROR,
        E_USER_WARNING      => Logger::WARNING,
        E_USER_NOTICE       => Logger::INFO,
        E_STRICT            => Logger::WARNING,
        E_RECOVERABLE_ERROR => Logger::ERROR,
        E_DEPRECATED        => Logger::WARNING,
        E_USER_DEPRECATED   => Logger::WARNING,
    ];

    private const ERROR_MAP = [
        E_ERROR             => 'E_ERROR',
        E_WARNING           => 'E_WARNING',
        E_PARSE             => 'E_PARSE',
        E_NOTICE            => 'E_NOTICE',
        E_CORE_ERROR        => 'E_CORE_ERROR',
        E_CORE_WARNING      => 'E_CORE_WARNING',
        E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
        E_USER_ERROR        => 'E_USER_ERROR',
        E_USER_WARNING      => 'E_USER_WARNING',
        E_USER_NOTICE       => 'E_USER_NOTICE',
        E_STRICT            => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED        => 'E_DEPRECATED',
        E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
    ];

    /** @var SimpleLogger */
    private $logger;

    private function __construct()
    {
        // noop
    }

    public static function create(SimpleLogger $logger): self
    {
        $handler = new self();

        $handler->logger = $logger;

        set_error_handler(          [$handler, 'handleError']);
        set_exception_handler(      [$handler, 'handleException']);
        register_shutdown_function( [$handler, 'handleShutdown']);

        return $handler;
    }

    public function handleError($code, $message, $file, $line)
    {
        throw new \ErrorException($message, 0, $code, $file, $line);
    }

    public function handleException(\Throwable $e)
    {
        $stackTrace[] = sprintf('%s:%s', $e->getFile(), $e->getLine());
        foreach ($e->getTrace() as $trace) {
            $stackTrace[] = sprintf(
                '%s:%s',
                $trace['file'] ?? '(no file)',
                $trace['line'] ?? '(no line)'
            );
        }

        $this->logger->log(
            Logger::ERROR,
            sprintf(
                'Uncaught Exception %s: %s at %s line %s',
                get_class($e), // Could possibly generate a new error
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ),
            [
                'stacktrace' => array_reverse($stackTrace),
            ]
        );
    }

    public function handleShutdown()
    {
        $lastError = error_get_last();

        if (! $lastError) {
            return;
        }

        $codeString = self::ERROR_MAP[$lastError['type']] ?? 'Unknown PHP error';

        $this->logger->log(
            self::LEVEL_MAP[$lastError['type']] ?? Logger::ERROR,
            sprintf(
                'Fatal Error %s: %s in %s line %s',
                $codeString,
                $lastError['message'],
                $lastError['file'] ?? '(null)',
                $lastError['line'] ?? '(null)'
            )
        );
    }
}
