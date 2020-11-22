<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

/**
 * Get or set session variable.
 *
 * @return mixed
 */
function session(string $name, $newValue = null)
{
    // todo: validate session started
    if ($newValue === null) {
        return $_SESSION[$name] ?? null;
    }

    $_SESSION[$name] = $newValue;
    return null;
}
