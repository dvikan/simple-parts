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

    public function getFileName(): string
    {
        return $this->filePath;
    }

    public function getBaseName(): string
    {
        return pathinfo($this->filePath, PATHINFO_FILENAME);
    }

    public function getExtension(): string
    {
        return pathinfo($this->filePath, PATHINFO_EXTENSION);
    }

    public function getRealPath(): string
    {
        return $this->filePath;
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

    public function delete(): void
    {
        $this->data = '';
        $this->modificationTime = time();
    }

    public function getModificationTime(): int
    {
        return $this->modificationTime;
    }
}
