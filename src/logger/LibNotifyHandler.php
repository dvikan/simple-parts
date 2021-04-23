<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class LibNotifyHandler implements Handler
{
    public function handle(array $record): void
    {
        $title = $record['channel'] . '.' . $record['level_name'];

        $shell = new Shell();
        $shell->execute('notify-send -t', ['15000', $title, $record['message']]);
    }
}
