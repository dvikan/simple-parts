<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class ErrorHandler
{
    const OPTIONS = [
    ];

    private $options;
    private $logger;

    private function __construct() {}

    public static function initialize(Logger $logger = null, array $options = [])
    {
        $errorHandler = new self();

        $errorHandler->logger = $logger ?: new NullLogger();
        $errorHandler->options = array_merge(self::OPTIONS, $options);

        set_error_handler([$errorHandler, 'handleError']);
        set_exception_handler([$errorHandler, 'handleException']);
        register_shutdown_function([$errorHandler, 'handleShutdown']);

        return $errorHandler;
    }

    public function handleError($code, $message, $file, $line)
    {
        $errorStrings = [
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_NOTICE => 'Notice',
            E_DEPRECATED => 'Deprecated',
            E_RECOVERABLE_ERROR => 'Recoverable error',
        ];

        $this->logger->log(
            SimpleLogger::ERROR,
            sprintf('%s: %s in %s:%s', $errorStrings[$code] ?? $code, $message, $file, $line)
        );

        exit(1);
    }

    public function handleException($e)
    {
        $this->logger->log(
            SimpleLogger::ERROR,
            sprintf('Uncaught Exception %s: "%s" at %s line %s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine())
        );

        exit(1);
    }

    public function handleShutdown()
    {
        $err = error_get_last();

        if ($err) {
            $this->logger->log(SimpleLogger::ERROR, $err['message']);
        }
    }
}
