<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class TextFile
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
        if (! $this->exists()) {
            throw new SimpleException(sprintf('File do not exists: "%s"', $this->filePath));
        }

        $data = file_get_contents($this->filePath);

        if ($data === false) {
            throw new SimpleException(sprintf('Unable to read from "%s"', $this->filePath));
        }

        return $data;
    }

    public function write(string $data): void
    {
        $result = file_put_contents($this->filePath, $data, LOCK_EX);

        if ($result === false) {
            throw new SimpleException(sprintf('Unable to write to "%s"', $this->filePath));
        }
    }

    public function append(string $data): void
    {
        $result = file_put_contents($this->filePath, $data, FILE_APPEND | LOCK_EX);

        if ($result === false) {
            throw new SimpleException(sprintf('Unable to append to "%s"', $this->filePath));
        }
    }
}
