<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Console
{
    private const NC = "\033[0m";
    private const GREEN = "\033[0;32m";
    private const YELLOW = "\033[1;33m";
    private const RED = "\033[0;31m";

    public function __construct()
    {
        // todo: add option for escaping ansi codes
    }

    public function println(string $s = '', ...$args)
    {
        $this->print($s . "\n", ...$args);
    }

    public function print(string $s, ...$args)
    {
        printf($s, ...$args);
    }

    public function exit(int $status = 0)
    {
        if ($status < 0 || $status > 254) {
            throw new SimpleException();
        }
        exit($status);
    }

    public function greenln(string $s, ...$args)
    {
        $this->green($s . "\n", ...$args);
    }

    public function green(string $s, ...$args)
    {
        $this->print(self::GREEN . $s . self::NC, ...$args);
    }

    public function yellow(string $s, ...$args)
    {
        $this->print(self::YELLOW . $s . self::NC, ...$args);
    }

    public function yellowln(string $s, ...$args)
    {
        $this->yellow($s . "\n", ...$args);
    }

    public function red(string $s, ...$args)
    {
        $this->print(self::RED . $s . self::NC, ...$args);
    }

    public function redln(string $s, ...$args)
    {
        $this->red($s . "\n", ...$args);
    }

    public function table(array $headers, array $rows, int $maxWidth = 50)
    {
        // Find the longest column value
        $columnWidth = 3;
        foreach (array_merge($rows, [$headers]) as $values) {
            foreach ($values as $value) {
                $columnWidth = max($columnWidth, mb_strlen((string) $value));
            }
        }
        // Enforce a maximum of 30
        $columnWidth = min($columnWidth, $maxWidth);

        // Truncate header values
        foreach ($headers as $i => $header) {
            $headers[$i] = $this->truncate((string) $header, $columnWidth);
        }

        // Truncate row values
        foreach ($rows as $i => $row) {
            foreach ($row as $j => $value) {
                $rows[$i][$j] = $this->truncate((string) ($value ?? 'NULL'), $columnWidth);
            }
        }

        // Create bar and row formatter
        $bar = '';
        $format = '';
        foreach ($headers as $value) {
            $bar .= '+' . str_repeat('-', $columnWidth +2);
            $format .= '| %-' . ($columnWidth +1 + strlen($value) - mb_strlen($value)) . 's';
        }
        $bar .= '+';
        $format .= '|';


        // Render bar
        $this->println($bar);

        // Render header
        $this->println($format, ...$headers);

        // Render bar
        $this->println($bar);

        // Render rows
        foreach ($rows as $row) {
            $format = '';
            foreach ($row as $value) {
                $format .= '| %-' . ($columnWidth +1 + strlen($value) - mb_strlen($value)) . 's';
            }
            $format .= '|';
            $this->println($format, ...$row);
        }

        // Render bar
        $this->println($bar);
    }

    private function truncate(string $str, int $length, $placeholder = '..'): string
    {
        if (mb_strlen($str) > $length) {
            return mb_substr($str, 0, $length - mb_strlen($placeholder)) . $placeholder;
        }

        return $str;
    }
}
