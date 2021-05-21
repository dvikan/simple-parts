<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

interface Http
{
    public const OK                     = 200;
    public const CREATED                = 201;
    public const MOVED_PERMANENTLY      = 301;
    public const FOUND                  = 302;
    public const SEE_OTHER              = 303;
    public const NOT_MODIFIED           = 304;
    public const TEMPORARY_REDIRECT     = 307;
    public const BAD_REQUEST            = 400;
    public const UNAUTHORIZED           = 401;
    public const NOT_FOUND              = 404;
    public const METHOD_NOT_ALLOWED     = 405;
    public const TOO_MANY_REQUESTS      = 429;
    public const INTERNAL_SERVER_ERROR  = 500;
    public const SERVICE_UNAVAILABLE    = 503;

    public const STATUS_LINES = [
        self::OK                    => '200 OK',
        self::CREATED               => '201 Created',
        self::MOVED_PERMANENTLY     => '301 Moved Permanently',
        self::FOUND                 => '302 Found',
        self::SEE_OTHER             => '303 See Other',
        self::NOT_MODIFIED          => '304 Not Modified',
        self::TEMPORARY_REDIRECT    => '307 Temporary Redirect',
        self::BAD_REQUEST           => '400 Bad Request',
        self::UNAUTHORIZED          => '401 Unauthorized',
        self::NOT_FOUND             => '404 Not Found',
        self::METHOD_NOT_ALLOWED    => '405 Method Not Allowed',
        self::TOO_MANY_REQUESTS     => '429 Too Many Requests',
        self::INTERNAL_SERVER_ERROR => '500 Internal Server Error',
        self::SERVICE_UNAVAILABLE   => '503 Service Unavailable',
    ];

    public const CONTENT_TYPE       = 'content-type';
    public const LOCATION           = 'location';

    // content types
    public const APPLICATION_JSON = 'application/json';

    // response headers
    public const CONTENT_LENGTH = 'content-length';
}