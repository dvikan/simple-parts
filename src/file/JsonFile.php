<?php

namespace dvikan\SimpleParts;

final class JsonFile implements File
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
        if ($data === '') return '';

        return $decode = $this->decode($data);
        return $data;
    }

    /**
     * @throws FileException
     */
    public function write(string $data): void
    {
        if (file_put_contents($this->filePath, $this->encode($data), LOCK_EX) === false) {
            throw new FileException(sprintf('Unable to write to "%s"', $this->filePath));
        }
    }

    public function append(string $data): void
    {
        if ($this->exists()) {
            $old = $this->decode($this->read());
            $new = Json::encode($data);

            $data = array_merge($old, $new);
        }

        if (file_put_contents($this->filePath, $this->encode($data), LOCK_EX) === false) {
            throw new FileException(sprintf('Unable to write to "%s"', $this->filePath));
        }
    }

    private function decode(string $data)
    {
        try {
            return Json::decode($data);
        } catch (SimpleException $e) {
            throw new FileException($e->getMessage());
        }
    }

    private function encode($data): string
    {
        try {
            return Json::encode($data);
        } catch (SimpleException $e) {
            throw new FileException($e->getMessage());
        }
    }
}
