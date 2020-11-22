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
        throw new FileException('NullFile doesnt exists');
    }

    public function write(string $data): void
    {
        // noop
    }

    public function append(string $data): void
    {
        // noop
    }
}
