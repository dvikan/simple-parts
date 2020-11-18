<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class LibNotifyHandler
{
    public function handle(array $record)
    {
        $title = escapeshellarg($record['channel'] . '.' . $record['level_name']);
        $message = escapeshellarg($record['message']);

        system(sprintf('notify-send -t 15000 %s %s', $title, $message));
    }
}
