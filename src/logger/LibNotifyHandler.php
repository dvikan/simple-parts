<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class LibNotifyHandler implements Handler
{
    private $level;

    public function __construct(int $level = SimpleLogger::INFO)
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

        guard(system(sprintf('notify-send -t 15000 %s %s', $title, $message)));
    }
}
