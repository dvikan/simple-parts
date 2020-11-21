<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class NullFile implements File
{
    public function exists(): bool
    {
        return false;
    }

    public function read(): string
    {
        return '';
    }

    public function write(string $data): void
    {
    }

    public function append(string $data): void
    {
    }
}
