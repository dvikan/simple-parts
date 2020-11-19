<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class PrintHandler implements Handler
{
    public function handle(array $record)
    {
        fprintf(
            STDERR,
            "[%s] %s.%s %s\n",
            (new \DateTime())->format('Y-m-d H:i:s'),
            $record['channel'],
            $record['level_name'],
            $record['message']
        );
    }
}
