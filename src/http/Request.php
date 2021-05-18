<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Request
{
    private $get;
    private $post;
    private $server;

    public function __construct(array $get = [], array $post = [], array $server = [])
    {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
    }

    public static function fromGlobals(): Request
    {
        return new self($_GET, $_POST, $_SERVER);
    }

    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function method(): string
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'];

        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        return ($uri);
    }

    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'];
    }
    
    public function get(string $key, string $default = null): ?string
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, string $default = null): ?string
    {
        return $this->post[$key] ?? $default;
    }

    public function postArray(string $key, array $default = null): ?array
    {
        return $this->post[$key] ?? $default;
    }
}
