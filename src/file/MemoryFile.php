<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class MemoryFile implements File
{
    private $memory;

    public function __construct()
    {
        $this->memory = '';
    }

    public function exists(): bool
    {
        return true;
    }

    public function read(): string
    {
        return $this->memory;
    }

    public function write(string $data): void
    {
        $this->memory = $data;
    }

    public function append(string $data): void
    {
        $this->memory .= $data;
    }
}
