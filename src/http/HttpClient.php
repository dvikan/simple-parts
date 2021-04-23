<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

interface HttpClient
{
    public const GET    = 'get';
    public const POST   = 'post';

    public const OK                     = 200;
    public const FOUND                  = 302;
    public const BAD_REQUEST            = 400;
    public const UNAUTHORIZED           = 401;
    public const INTERNAL_SERVER_ERROR  = 500;

    public const STATUS_LINES = [
        self::OK                    => '200 OK',
        self::FOUND                 => '302 Found',
        self::BAD_REQUEST           => '400 Bad Request',
        self::UNAUTHORIZED          => '401 Unauthorized',
        self::INTERNAL_SERVER_ERROR => '500 Internal Server Error',
    ];

    public const BODY               = 'body';
    public const USERAGENT          = 'useragent';
    public const CONNECT_TIMEOUT    = 'connect_timeout';
    public const TIMEOUT            = 'timeout';
    public const CLIENT_ID          = 'client_id';
    public const AUTH_BEARER        = 'auth_bearer';

    public const LOCATION           = 'location';

    public function get(string $url, array $options = []): Response;

    public function post(string $url, array $options = []): Response;
}
