<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class SystemClock implements Clock
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}