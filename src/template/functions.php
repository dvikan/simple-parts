<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

function render(string $template, array $vars = []): string
{
    extract($vars);
    ob_start();
    require $template;
    return ob_get_clean();
}

/**
 * Escape string for html context
 */
function e(string $s): string
{
    return htmlspecialchars($s);
}
