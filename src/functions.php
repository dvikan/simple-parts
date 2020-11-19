<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use Closure;


/**
 * @throws SimpleException
 */
function retry(int $count, Closure $fn, Closure $fail)
{
    while ($count !== 0) {
        try {
            $fn();
        } catch (SimpleException $e) {
            $count--;
        }
    }

    $fail($e);
}

/**
 * Guard aginst false values
 */
function guard($value, $message = 'Value was false')
{
    if ($value === false) {
        throw new SimpleException($message);
    }

    return $value;
}

function render(string $template, array $vars = []): string
{
    extract($vars);
    ob_start();
    require $template;
    return ob_get_clean();
}

/**
 * Helper function for creating a Response
 */
function response(string $body = '', int $code = 200, array $headers = []): Response
{
    return new Response($body, $code, $headers);
}

/**
 * Escape string for html context
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
    // todo: validate session started
    if ($value === null) {
        return $_SESSION[$name] ?? null;
    }

    $_SESSION[$name] = $value;
}
