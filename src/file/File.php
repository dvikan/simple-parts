<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

interface File
{
    public function name(): string;

    public function exists(): bool;

    public function read(): string;

    public function write(string $data): void;

    public function append(string $data): void;

    public function delete(): void;
}