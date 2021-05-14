<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class SimpleException extends \Exception
{
}

/**
 * Escape for html context
 */
function e(string $s): string
{
    return htmlspecialchars($s);
}

/**
 * Explicitly don't escape
 */
function raw(string $s): string
{
    return $s;
}