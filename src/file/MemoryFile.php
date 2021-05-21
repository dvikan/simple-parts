<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class MemoryFile implements File
{
    private $filePath;
    private $data;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->data = '';
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
    }

    public function append(string $data): void
    {
        $this->data .= $data;
    }

    public function name(): string
    {
        return $this->filePath;
    }

    public function delete(): void
    {
        $this->data = '';
    }
}
