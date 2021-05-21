<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class MemoryFile implements File
{
    private $filePath;
    private $data;
    private $modificationTime;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->data = '';
        $this->modificationTime = time();
    }

    public function exists(): bool
    {
        return true;
    }

    public function read(): string
    {
        return $this->data;
    }

    public function write(string $data): void
    {
        $this->data = $data;
        $this->modificationTime = time();
    }

    public function append(string $data): void
    {
        $this->data .= $data;
        $this->modificationTime = time();
    }

    public function name(): string
    {
        return $this->filePath;
    }

    public function delete(): void
    {
        $this->data = '';
        $this->modificationTime = time();
    }

    public function modificationTime(): int
    {
        return $this->modificationTime;
    }
}
