<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

/**
 * Stores log records in a file
 */
final class FileHandler implements Handler
{
    private $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function handle(array $record): void
    {
        if (isset($record['context']['exception'])) {
            $context = "\nStack trace:\n" . $record['context']['exception']->getTraceAsString();
        } else {
            $context = Json::encode($record['context'] ?: []);
        }

        $item = sprintf(
            "[%s] %s.%s %s %s\n",
            $record['datetime']->format('Y-m-d H:i:s'),
            $record['channel'],
            $record['level_name'],
            $record['message'],
            $context,
        );

        $this->file->append($item);
    }
}
