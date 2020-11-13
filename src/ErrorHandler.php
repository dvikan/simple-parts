<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use Throwable;

final class ErrorHandler
{
    const OPTIONS = [
        'print_errors' => true,
        'exit_on_error' => true,
        'error_log' => '/dev/null',
    ];

    private $options;
    /** @var Logger */
    private $logger;

    private function __construct() {}

    public static function initialize(array $options = []): void
    {
        $errorHandler = new self();
        $errorHandler->options = array_merge(self::OPTIONS, $options);

        $errorHandler->logger = new Logger($errorHandler->options['error_log']);

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

        $message = sprintf("%s: %s in %s:%s", $errors[$errno] ?? $errno, $errstr, $errfile, $errline);

        $this->logger->log($message);

        if ($this->options['print_errors']) {
            print $message . PHP_EOL;
        }

        if ($this->options['exit_on_error']) {
            exit(1);
        }
    }

    public function handleException(Throwable $e)
    {
        $message = sprintf('Uncaught %s: "%s" in %s:%s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());

        $this->logger->log($message);

        if ($this->options['print_errors']) {
            print $message . PHP_EOL;
        }

        exit(1); // Explicit exit for the status code
    }

    public function handleShutdown()
    {
        $err = error_get_last();
        if ($err) {
            $this->logger->log(implode(', ', $err));
            var_dump($err);
        }
    }
}
