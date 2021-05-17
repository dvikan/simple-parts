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

        try {
            clearstatcache();
            $this->stat = stat($filePath);
        } catch (\ErrorException $e) {
            $this->stat = [];
        }
    }

    public function name(): string
    {
        return $this->filePath;
    }

    public function exists(): bool
    {
        return file_exists($this->filePath);
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
        try {
            clearstatcache();
            $stat = stat($this->filePath);
        } catch (\ErrorException $e) {
            $stat = [];
        }

        if ($this->exists()) {
            if ($this->stat['mtime'] !== $stat['mtime']) {
                throw new SimpleException(sprintf('The file was modified during runtime: "%s"', $this->filePath));
            }
        }

        try {
            $a = file_put_contents($this->filePath, $data, $flags);
        } catch(\ErrorException $e) {
            throw new SimpleException($e->getMessage());
        }

        clearstatcache();
        $this->stat = stat($this->filePath);
    }

    public function delete(): void
    {
        unlink($this->filePath);
    }
}
