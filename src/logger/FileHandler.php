<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class FileHandler
{
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle(array $record)
    {
        file_put_contents(
            $this->filePath,
            sprintf("[%s] %s.%s %s\n", $record['datetime']->format('Y-m-d H:i:s'), $record['channel'], $record['level_name'], $record['message']),
            FILE_APPEND
        );
    }
}
