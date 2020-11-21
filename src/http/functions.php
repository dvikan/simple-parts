<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

/**
 * Helper function for creating a Response
 */
function response(string $body = '', int $code = 200, array $headers = []): Response
{
    return new Response($body, $code, $headers);
}
