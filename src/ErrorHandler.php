<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use Throwable;

final class ErrorHandler
{
    const OPTIONS = [
        'print_errors' => true,
        'exit_on_error' => true,
    ];

    private $options;

    private function __construct() {}

    public static function initialize(array $options = []): void
    {
        $errorHandler = new self();
        $errorHandler->options = array_merge(self::OPTIONS, $options);

        set_error_handler([$errorHandler, 'handleError']);
        set_exception_handler([$errorHandler, 'handleException']);
    }

    public function handleError($errno, $errstr, $errfile, $errline)
    {
        $errors = [
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_NOTICE => 'Notice',
            E_DEPRECATED => 'Deprecated',
        ];

        if ($this->options['print_errors']) {
            printf("%s: %s in %s:%s\n", $errors[$errno] ?? $errno, $errstr, $errfile, $errline);
        }

        if ($this->options['exit_on_error']) {
            exit(1);
        }
    }

    public function handleException(Throwable $e)
    {
        if ($this->options['print_errors']) {
            printf("Uncaught %s: %s in %s:%s\n", get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
        }

        exit(1); // Explicit exit for the status code
    }
}
