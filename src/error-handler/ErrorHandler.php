<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class ErrorHandler
{
    // Mapping from php error code to string
    private const ERROR_MAP = [
        E_ERROR             => 'Error',
        E_WARNING           => 'Warning',
        E_NOTICE            => 'Notice',
        E_RECOVERABLE_ERROR => 'Recoverable error',
        E_DEPRECATED        => 'Deprecated',
    ];

    // Mapping from php error code to logger log level
    private const LEVEL_MAP = [
        E_NOTICE            => Logger::INFO,
        E_WARNING           => Logger::WARNING,
    ];

    // Exit on these php error codes
    private const EXIT_MAP = [
        E_NOTICE            => true,
        E_WARNING           => true,
        E_RECOVERABLE_ERROR => true,
    ];

    /** @var Logger */
    private $logger;

    private function __construct()
    {
        // noop
    }

    public static function create(Logger $logger): self
    {
        $errorHandler = new self();

        $errorHandler->logger = $logger;

        set_error_handler([$errorHandler, 'handleError']);
        set_exception_handler([$errorHandler, 'handleException']);
        register_shutdown_function([$errorHandler, 'handleShutdown']);

        return $errorHandler;
    }

    public function handleError($code, $message, $file, $line)
    {
        $level = self::LEVEL_MAP[$code] ?? Logger::ERROR;
        $codeName = self::ERROR_MAP[$code] ?? ((string)$code);

        $formattedMessage = sprintf(
            '%s: %s in %s:%s',
            $codeName,
            $message,
            $file,
            $line
        );

        $context = [
            'debug' => [
                'message'       => $message,
                'code'          => $code,
                'file'          => $file,
                'line'          => $line,
                'stacktrace'    => $this->createStackTraceFromTrace(debug_backtrace()),
            ]
        ];

        $this->logger->log($level, $formattedMessage, $context);

        // Whether to exit based off of php error codes
        if (self::EXIT_MAP[$code]) {
            exit(1);
        }
    }

    public function handleException($exception)
    {
        $level = Logger::ERROR;
        $exceptionMessage = $exception->getMessage();
        $code = $exception->getCode();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $exceptionClass = get_class($exception);

        $message = sprintf('Exception %s: %s in %s:%s', $exceptionClass, $exceptionMessage, $file, $line);

        $context = [
            'debug' => [
                'message'       => $exceptionMessage,
                'code'          => $code,
                'file'          => $file,
                'line'          => $line,
                'stacktrace'    => $this->createStackTraceFromTrace($exception->getTrace()),
            ]
        ];

        // Append the stack frame where the exception was thrown
        $context['debug']['stacktrace'][] = sprintf('%s %s:%s', $exceptionClass, $file, $line);

        $this->logger->log($level, $message, $context);

        exit(1); // I don't think this is necessary because the php engine quits after an exception
    }

    public function handleShutdown()
    {
        $err = error_get_last();

        if (! $err) {
            return;
        }

        $errorMessage = $err['message'] ?? '';
        $code = $err['type'] ?? '';
        $file = $err['file'] ?? '';
        $line = $err['line'] ?? '';

        $level = self::LEVEL_MAP[$code] ?? Logger::ERROR;
        $codeName = self::ERROR_MAP[$code] ?? ((string)($code));

        $message = sprintf(
            '%s: %s in %s:%s',
            $codeName,
            $errorMessage,
            $file,
            $line
        );

        $context = [
            'debug' => [
                'message'       => $errorMessage,
                'code'          => $code,
                'file'          => $file,
                'line'          => $line,
                'stacktrace'    => $this->createStackTraceFromTrace(debug_backtrace()),
            ]
        ];

        $this->logger->log($level, $message, $context);
    }

    private function createStackTraceFromTrace(array $backTrace): array
    {
        $stackTrace = [];

        foreach ($backTrace as $trace) {
            $file = $trace['file'] ?? '';
            $line = $trace['line'] ?? '';
            $function = $trace['function'] ?? '';
            $class = $trace['class'] ?? '';
            //$object = $trace['object'];
            $type = $trace['type'] ?? '';
            //$args = $trace['args'];

            $stackTrace[] = sprintf(
                '%s%s%s() %s:%s',
                $class,
                $type,
                $function,
                $file,
                $line
            );
        }

        return array_reverse($stackTrace);
    }
}
