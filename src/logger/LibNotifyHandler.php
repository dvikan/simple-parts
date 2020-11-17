<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class LibNotifyHandler
{
    public function handle(string $severity, string $message)
    {
        system(sprintf("notify-send -t 15000 %s %s", 'Logger', escapeshellarg(sprintf('%s %s', $severity, $message))));
    }
}
