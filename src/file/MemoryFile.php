<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class MemoryFile implements File
{
    private $data = '';

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
        return ':memory:';
    }
}
