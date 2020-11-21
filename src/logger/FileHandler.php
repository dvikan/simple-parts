<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class FileHandler implements Handler
{
    private $file;
    private $level;

    public function __construct(File $file, int $level = Logger::INFO)
    {
        $this->file = $file;
        $this->level = $level;
    }

    public function handle(array $record)
    {
        if ($record['level'] < $this->level) {
            return;
        }

        $line = sprintf(
            "[%s] %s.%s %s %s",
            $record['datetime']->format('Y-m-d H:i:s'),
            $record['channel'],
            $record['level_name'],
            $record['message'],
            $record['context'] ? Json::encode($record['context']) : '',
        );

        $this->file->append($line);
    }
}
