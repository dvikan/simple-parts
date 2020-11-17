<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTime;

class FileHandler
{
    private $filepath;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }

    public function handle(string $severity, string $message)
    {
        $now = new DateTime();
        $line = sprintf("[%s] %s %s\n", $now->format('Y-m-d H:i:s'), $severity, $message);
        file_put_contents($this->filepath, $line, FILE_APPEND);
    }
}
