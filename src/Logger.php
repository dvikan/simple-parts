<?php declare(strict_types=1);

namespace StaticParts;

use DateTimeImmutable;

class Logger
{
    /** @var string */
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function log(string $message, string $channel = 'default')
    {
        $now = new DateTimeImmutable;

        $line = sprintf("%s %s: %s\n", $now->format('Y-m-d H:i:s'), $channel, $message);

        // todo: flock
        file_put_contents($this->path, $line, FILE_APPEND);
    }
}