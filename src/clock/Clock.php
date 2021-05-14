<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
