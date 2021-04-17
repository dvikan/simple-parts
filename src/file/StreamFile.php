<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use const LOCK_EX;

final class StreamFile implements File
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
            return '';
        }

        $data = file_get_contents($this->filePath);

        if ($data === false) {
            throw new SimpleException(sprintf('Unable to read from "%s"', $this->filePath));
        }

        return $data;
    }

    public function write(string $data): void
    {
        if (file_put_contents($this->filePath, $data, LOCK_EX) === false) {
            throw new SimpleException(sprintf('Unable to write to "%s"', $this->filePath));
        }
    }

    public function append(string $data): void
    {
        if (file_put_contents($this->filePath, $data, FILE_APPEND | LOCK_EX) === false) {
            throw new SimpleException(sprintf('Unable to write to "%s"', $this->filePath));
        }
    }
}
