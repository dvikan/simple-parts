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

/**
 * Sanitize for html context. Remove tags. Don't convert quotes to entities.
 */
function sanitize(string $s): string
{
    return filter_var($s, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
}

/**
 * Truncate string
 */
function truncate(string $s, int $length = 400, $marker = '...'): string
{
    $s = trim($s);

    if (mb_strlen($s) < $length) {
        return $s;
    }

    $naiveTruncate = mb_substr($s, 0, $length);

    $lastSpace = mb_strrpos($naiveTruncate, ' ');

    if ($lastSpace === false) {
        $lastSpace = $length;
    }

    $properTruncate = mb_substr($s, 0, $lastSpace);

    return $properTruncate . $marker;
}