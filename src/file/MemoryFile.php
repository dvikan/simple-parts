<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class MemoryFile implements File
{
    /**
     * @var array
     */
    private $data;

    public function __construct()
    {
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
}
