<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class ErrorHandler
{
    /** @var Logger */
    private $logger;

    private function __construct() {}

    public static function create(Logger $logger = null)
    {
        $errorHandler = new self();

        $errorHandler->logger = $logger ?: new SimpleLogger();

        set_error_handler([$errorHandler, 'handleError']);
        set_exception_handler([$errorHandler, 'handleException']);
        register_shutdown_function([$errorHandler, 'handleShutdown']);

        return $errorHandler;
    }

    public function handleError($code, $message, $file, $line)
    {
        $this->logger->log(
            $this->codeToLevel($code),
            sprintf(
                '%s: %s in %s:%s',
                $this->codeToString($code),
                $message,
                $file,
                $line
            )
        );

        exit(1);
    }

    public function handleException($e)
    {
        $this->logger->log(
            Logger::ERROR, // An exception is always an error
            sprintf(
                'Uncaught Exception %s: "%s" at %s line %s',
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            )
        );

        exit(1);
    }

    public function handleShutdown()
    {
        $err = error_get_last();

        if ($err) {
            $this->logger->log(
                $this->codeToLevel($err['code']),
                $err['message']
            );
        }
    }

    private function codeToLevel($code): int
    {
        static $map = [
            E_NOTICE => Logger::INFO,
        ];
        return $map[$code] ?? Logger::ERROR;
    }

    private function codeToString(int $code): string
    {
        static $map = [
            E_ERROR             => 'Error',
            E_WARNING           => 'Warning',
            E_NOTICE            => 'Notice',
            E_RECOVERABLE_ERROR => 'Recoverable error',
            E_DEPRECATED        => 'Deprecated',
        ];
        return $map[$code] ?? (string) $code;
    }
}
