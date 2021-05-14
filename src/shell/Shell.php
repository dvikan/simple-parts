<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Shell
{
    public function execute(string $command, array $arguments = []): string
    {
        foreach ($arguments as $argument) {
            $command .= ' ' . escapeshellarg($argument);
        }

        $_ = exec($command, $output, $status);

        switch ($status) {
            case 0:
                return implode("\n", $output);
            case 127:
                throw new SimpleException(sprintf('Not found: "%s"', $command));
            default:
                throw new SimpleException(sprintf('Unsuccessful: "%s"', $command));
        }
    }
}
