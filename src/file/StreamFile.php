<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use const LOCK_EX;

final class StreamFile implements File
{
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function exists(): bool
    {
        return file_exists($this->filePath) === true;
    }

    /**
     * @throws FileException
     */
    public function read(): string
    {
        if (!$this->exists()) {
            throw new FileException(sprintf('File "%s" doesnt exists', $this->filePath));
        }

        $data = file_get_contents($this->filePath);

        if ($data === false) {
            throw new FileException(sprintf('Unable to read from "%s"', $this->filePath));
        }

        return $data;
    }

    /**
     * @throws FileException
     */
    public function write(string $data): void
    {
        if (file_put_contents($this->filePath, $data, LOCK_EX) === false) {
            throw new FileException(sprintf('Unable to write to "%s"', $this->filePath));
        }
    }

    /**
     * @throws FileException
     */
    public function append(string $data): void
    {
        if (file_put_contents($this->filePath, $data, FILE_APPEND | LOCK_EX) === false) {
            throw new FileException(sprintf('Unable to write to "%s"', $this->filePath));
        }
    }
}
