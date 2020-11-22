<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Shell
{
    public function execute(string $program, array $args = []): string
    {
        $command = $program . ' ' . implode(' ', $args);

        exec(escapeshellcmd($command), $output, $status);

        if ($status === 0) {
            return implode("\n", $output);
        }

        switch ($status) {
            case 127:
                throw new SimpleException('Command not found: ' . $command);
            default:
                throw new SimpleException("exec(): $status");
        }
    }
}
