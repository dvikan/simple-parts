<?php declare(strict_types=1);

namespace StaticParts;

function render(string $template, array $vars = []): string
{
    extract($vars);
    ob_start();
    require $template;
    return ob_get_clean();
}

/**
 * Escape variable for html context.
 */
function e(string $s): string
{
    return htmlspecialchars($s);
}

/**
 * Get or set session variable.
 */
function session(string $name, $value = null)
{
    if ($value === null) {
        return $_SESSION[$name] ?? null;
    }

    $_SESSION[$name] = $value;
}
