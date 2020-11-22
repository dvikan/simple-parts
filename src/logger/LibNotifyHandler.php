<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class LibNotifyHandler implements Handler
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

        $title = $record['channel'] . '.' . $record['level_name'];

        $shell = new Shell();
        $shell->execute('notify-send -t', [15000, $title, $record['message']]);
    }
}
