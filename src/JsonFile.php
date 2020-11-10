<?php

namespace dvikan\SimpleParts;

final class JsonFile
{
    private $filePath;

    private function __construct() {}

    public static function fromFile(string $filePath)
    {
        $jsonFile = new self();
        $jsonFile->filePath = $filePath;
        return $jsonFile;
    }

    public function read(): array
    {
        if (!file_exists($this->filePath)) {
            $this->write([]);
        }

        $content = file_get_contents($this->filePath);

        if ($content === false) {
            throw new SimpleException();
        }

        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    public function write(array $data): void
    {
        $json = json_encode(
            $data,
            JSON_THROW_ON_ERROR
            | JSON_PRETTY_PRINT
            | JSON_UNESCAPED_SLASHES
        );

        if ($json === false) {
            throw new SimpleException();
        }

        $bytesWritten = file_put_contents($this->filePath, $json);

        if ($bytesWritten === false) {
            throw new SimpleException();
        }
    }
}
