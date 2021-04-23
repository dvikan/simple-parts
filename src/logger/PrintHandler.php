<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class PrintHandler implements Handler
{
    public function handle(array $record): void
    {
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
