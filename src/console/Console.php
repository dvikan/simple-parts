<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Console
{
    private const NC = "\033[0m";
    private const GREEN = "\033[0;32m";
    private const YELLOW = "\033[1;33m";
    private const RED = "\033[0;31m";

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
        $this->println($bar);
        $this->println($format, ...$headers);
        $this->println($bar);

        foreach ($rows as $row) {
            $this->println($format, ...$row);
        }

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
