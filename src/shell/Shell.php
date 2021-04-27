<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Shell
{
    /**
     * @param string[] $arguments
     */
    public function execute(string $command, array $arguments = []): string
    {
        array_walk($arguments, function ($n) {
            if (! is_string($n)) {
                throw new SimpleException('All arguments must be strings. Got: ' . $n . ' ' . gettype($n));
            }
        });

        foreach ($arguments as $argument) {
            $command .= ' ' . escapeshellarg($argument);
        }

        $_ = exec($command, $output, $status);

        if ($status === 0) {
            return implode("\n", $output);
        }

        switch ($status) {
            case 127:
                throw new SimpleException(sprintf('Not found: "%s"', $command));
            default:
                throw new SimpleException(sprintf('Unsuccessful: "%s"', $command));
        }
    }
}
