<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class TextFile implements File
{
    private $filePath;
    private $stat;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

        if (file_exists($filePath)) {
            clearstatcache();
            $this->stat = stat($filePath);
        } else {
            $this->stat = [];
        }
    }

    public function getFileName(): string
    {
        return basename($this->filePath);
    }

    public function getRealPath(): string
    {
        return realpath($this->filePath);
    }

    public function exists(): bool
    {
        clearstatcache();
        return file_exists($this->filePath);
    }

    public function getModificationTime(): int
    {
        clearstatcache();
        $stat = stat($this->filePath);
        return $stat['mtime'];
    }

    public function read(): string
    {
        if (! $this->exists()) {
            throw new SimpleException(sprintf('File do not exists: "%s"', $this->filePath));
        }

        return file_get_contents($this->filePath);
    }

    public function write(string $data): void
    {
        $this->_write($data, LOCK_EX);
    }

    public function append(string $data): void
    {
        $this->_write($data, LOCK_EX | FILE_APPEND);
    }

    protected function _write(string $data, int $flags): void
    {
        if ($this->exists()) {
            clearstatcache();
            $stat = stat($this->filePath);

            if ($stat['mtime'] !== $this->stat['mtime']) {
                throw new SimpleException(sprintf('The file was modified during runtime: "%s"', $this->filePath));
            }
        }
        
        file_put_contents($this->filePath, $data, $flags);

        clearstatcache();
        $this->stat = stat($this->filePath);
    }

    public function delete(): void
    {
        unlink($this->filePath);
    }
}
