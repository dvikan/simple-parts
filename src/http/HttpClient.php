<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

interface HttpClient
{
    public const USERAGENT         = 'useragent';
    public const CONNECT_TIMEOUT   = 'connect_timeout';
    public const TIMEOUT           = 'timeout';
    public const CLIENT_ID         = 'client_id';
    public const AUTH_BEARER       = 'auth_bearer';

    public function get(string $url): Response;

    public function post(string $url, array $vars = []): Response;
}
