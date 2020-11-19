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

        $contents = guard(file_get_contents($this->filePath));

        if ($contents === '') {
            return [];
        }

        return Json::decode($contents);
    }

    public function putContents(array $data): void
    {
        guard(file_put_contents($this->filePath, Json::encode($data), LOCK_EX));
    }
}
