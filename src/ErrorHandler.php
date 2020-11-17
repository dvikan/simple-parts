<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use Throwable;

final class ErrorHandler
{
    const OPTIONS = [
        'exit_on_error' => true,
    ];

    private $options;
    private $logger;

    private function __construct() {}

    public static function initialize($logger = null, array $options = []): void
    {
        $errorHandler = new self();

        $errorHandler->logger = $logger ?: new NullLogger();
        $errorHandler->options = array_merge(self::OPTIONS, $options);

        set_error_handler([$errorHandler, 'handleError']);
        set_exception_handler([$errorHandler, 'handleException']);
        register_shutdown_function([$errorHandler, 'handleShutdown']);
    }

    public function handleError($errno, $errstr, $errfile, $errline)
    {
        $errors = [
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_NOTICE => 'Notice',
            E_DEPRECATED => 'Deprecated',
            E_RECOVERABLE_ERROR => 'Recoverable error',
        ];

        $message = sprintf('%s: %s in %s:%s', $errors[$errno] ?? $errno, $errstr, $errfile, $errline);

        $this->logger->log(Logger::ERROR, $message);

        if ($this->options['exit_on_error']) {
            exit(1);
        }
    }

    public function handleException(Throwable $e)
    {
        $message = sprintf('Uncaught %s: "%s" in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());

        $this->logger->log(Logger::ERROR, $message);

        exit(1); // Explicit exit for the status code
    }

    public function handleShutdown()
    {
        $err = error_get_last();

        if ($err) {
            $message = implode(', ', $err);
            $this->logger->log(Logger::ERROR, $message);
        }
    }
}
