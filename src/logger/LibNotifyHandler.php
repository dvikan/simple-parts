<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class LibNotifyHandler
{
    private $level;

    public function __construct(int $level = Logger::INFO)
    {
        $this->level = $level;
    }

    public function handle(array $record)
    {
        if ($record['level'] < $this->level) {
            return;
        }

        $title = escapeshellarg($record['channel'] . '.' . $record['level_name']);
        $message = escapeshellarg($record['message']);

        system(sprintf('notify-send -t 15000 %s %s', $title, $message));
    }
}
