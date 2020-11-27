<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use function fprintf;
use const STDERR;

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

        printf(
            "%s.%s %s %s\n",
            $record['channel'],
            $record['level_name'],
            $record['message'],
            Json::encode($record['context'] ?: []),
        );
    }
}
