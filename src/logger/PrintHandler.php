<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class PrintHandler implements Handler
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
