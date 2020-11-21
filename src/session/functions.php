<?php declare(strict_types=1);

namespace dvikan\SimpleParts;


/**
 * Get or set session variable.
 */
function session(string $name, $value = null)
{
    // todo: validate session started
    if ($value === null) {
        return $_SESSION[$name] ?? null;
    }

    $_SESSION[$name] = $value;
}
