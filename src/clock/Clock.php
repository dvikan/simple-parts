<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTimeImmutable;

interface Clock
{
    public function now(): DateTimeImmutable;
}
