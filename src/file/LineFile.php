<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use function file;
use function file_exists;
use function file_put_contents;

final class LineFile implements File
{
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function exists(): bool
    {
        return file_exists($this->filePath) === true;
    }

    public function read(): string
    {
        if (!$this->exists()) {
            throw new SimpleException(sprintf('File "%s" doesnt exists', $this->filePath));
        }

        $lines = file($this->filePath);

        if ($lines === false) {
            throw new SimpleException(sprintf('Unable to read from "%s"', $this->filePath));
        }

        foreach ($lines as &$line) {
            $line = rtrim($line, "\n"); // perhaps not trim too much here
        }

        return implode('', $lines);
    }

    public function write(string $data): void
    {
        if (file_put_contents($this->filePath, $data . "\n", LOCK_EX) === false) {
            throw new SimpleException(sprintf('Unable to write to "%s"', $this->filePath));
        }
    }

    public function append(string $data): void
    {
        if (file_put_contents($this->filePath, $data . "\n", FILE_APPEND | LOCK_EX) === false) {
            throw new SimpleException(sprintf('Unable to write to "%s"', $this->filePath));
        }
    }
}
