<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class NullLogger
{
    public function log(string $severity, string $message)
    {
        // noop
    }
}
