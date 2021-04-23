<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class PrintHandler implements Handler
{
    private $level;

    public function __construct(int $level = Logger::INFO)
    {
        $this->level = $level;
    }

    public function handle(array $record): void
    {
        if ($record['level'] < $this->level) {
            return;
        }

        // Ignore this xdebug bug
        if (strpos($record['message'], 'Header may not contain NUL bytes') !== false) {
            return;
        }

        // Special case for records that are caused by exception
        if (isset($record['context']['exception'])) {
            $context = "\nStack trace:\n" . $record['context']['exception']->getTraceAsString();
        } else {
            $context = Json::encode($record['context'] ?: []);
        }

        printf(
            "%s.%s %s %s\n",
            $record['channel'],
            $record['level_name'],
            $record['message'],
            $context
        );
    }
}
