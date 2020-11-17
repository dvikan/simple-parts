<?php

namespace dvikan\SimpleParts;

final class JsonFile
{
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function getContents(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $contents = file_get_contents($this->filePath);

        if ($contents === false) {
            throw new SimpleException(sprintf('Unable to read contents of "%s"', $this->filePath));
        }

        if ($contents === '') {
            return [];
        }

        return Json::decode($contents);
    }

    public function putContents(array $data): void
    {
        $bytesWritten = file_put_contents($this->filePath, Json::encode($data));

        if ($bytesWritten === false || $bytesWritten === 0) {
            throw new SimpleException(sprintf('Unable to put contents to "%s"', $this->filePath));
        }
    }
}
