<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

interface Handler
{
    public function handle(array $record): void;
}
