<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class FileHandler implements Handler
{
    private $filePath;
    private $level;

    public function __construct(string $filePath, int $level = Logger::INFO)
    {
        $this->filePath = $filePath;
        $this->level = $level;
    }

    public function handle(array $record)
    {
        if ($record['level'] < $this->level) {
            return;
        }

        $line = sprintf(
            "[%s] %s.%s %s %s\n",
            $record['datetime']->format('Y-m-d H:i:s'),
            $record['channel'],
            $record['level_name'],
            $record['message'],
            $record['context'] ? Json::encode($record['context']) : '',
        );

        guard(file_put_contents($this->filePath, $line, FILE_APPEND | LOCK_EX));
    }
}
