<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

interface HttpClient
{
    public const OK                     = 200;
    public const BAD_REQUEST            = 400;
    public const INTERNAL_SERVER_ERROR  = 500;

    public const LINES = [
        self::OK                    => '200 OK',
        self::BAD_REQUEST           => '400 Bad Request',
        self::INTERNAL_SERVER_ERROR => '500 Internal Server Error',
    ];

    public const BODY               = 'body';
    public const USERAGENT          = 'useragent';
    public const CONNECT_TIMEOUT    = 'connect_timeout';
    public const TIMEOUT            = 'timeout';
    public const CLIENT_ID          = 'client_id';
    public const AUTH_BEARER        = 'auth_bearer';

    public function get(string $url, array $options = []): Response;

    public function post(string $url, array $options = []): Response;
}
