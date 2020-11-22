<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use function count;
use function max;
use function mb_strlen;
use function mb_substr;
use function min;
use function printf;
use function range;
use function str_repeat;

final class Console
{
    private const NC = "\033[0m";
    private const GREEN = "\033[0;32m";
    private const YELLOW = "\033[1;33m";
    private const RED = "\033[0;31m";

    public function write(string $line, ...$args)
    {
        printf($line, ...$args);
    }

    public function writeln(string $line = '', ...$args)
    {
        printf($line . "\n", ...$args);
    }

    public function exit(int $status = 0)
    {
        exit($status);
    }

    public function green(string $line, ...$args)
    {
        printf(self::GREEN . $line . self::NC, ...$args);
    }

    public function greenln(string $line, ...$args)
    {
        printf(self::GREEN . $line . self::NC . "\n", ...$args);
    }

    public function yellow(string $line, ...$args)
    {
        sprintf(self::YELLOW . $line . self::NC, ...$args);
    }

    public function yellowln(string $line, ...$args)
    {
        printf(self::YELLOW . $line . self::NC . "\n", ...$args);
    }

    public function red(string $line, ...$args)
    {
        printf(self::RED . $line . self::NC, ...$args);
    }

    public function redln(string $line, ...$args)
    {
        printf(self::RED . $line . self::NC . "\n", ...$args);
    }

    public function table(array $headers, array $rows)
    {
        // Find the longest column value
        $columnWidth = 3;
        foreach (array_merge($rows, [$headers]) as $values) {
            foreach ($values as $value) {
                $columnWidth = max($columnWidth, mb_strlen((string) $value));
            }
        }
        // Enforce a maximum of 30
        $columnWidth = min($columnWidth, 30);

        // Truncate header values
        foreach ($headers as $i => $header) {
            $headers[$i] = $this->truncate($header, $columnWidth);
        }

        // Truncate row values
        foreach ($rows as $i => $row) {
            foreach ($row as $j => $value) {
                $value = (string) $value;
                $rows[$i][$j] = $this->truncate($value, $columnWidth);
            }
        }

        // Create bar and row formatter
        $bar = '';
        $format = '';
        foreach (range(1, count($headers)) as $_) {
            $bar .= '+' . str_repeat('-', $columnWidth +2);
            $format .= '| %-' . ($columnWidth +1) . 's';
        }
        $bar .= '+';
        $format .= '|';

        // Render the table
        $this->writeln($bar);
        $this->writeln($format, ...$headers);
        $this->writeln($bar);

        foreach ($rows as $row) {
            $this->writeln($format, ...$row);
        }

        $this->writeln($bar);
    }

    private function truncate(string $str, int $length, $placeholder = '..'): string
    {
        if (mb_strlen($str) > $length) {
            return mb_substr($str, 0, $length - mb_strlen($placeholder)) . $placeholder;
        }

        return $str;
    }
}
