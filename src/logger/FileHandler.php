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
        $data = sprintf("[%s] %s.%s %s\n", $record['datetime']->format('Y-m-d H:i:s'), $record['channel'], $record['level_name'], $record['message']);

        guard(file_put_contents($this->filePath, $data, FILE_APPEND | LOCK_EX));
    }
}
