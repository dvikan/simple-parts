<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTime;

class PrintHandler
{
    public function handle(string $severity, string $message)
    {
        $now = new DateTime();
        fprintf(STDERR, "[%s] %s %s\n", $now->format('Y-m-d H:i:s'), $severity, $message);
    }
}
