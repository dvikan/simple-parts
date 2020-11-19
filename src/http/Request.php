<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class Request
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
        return new Request($_GET, $_POST, $_SERVER);
    }

    public function get(string $name): ?string
    {
        return $this->get[$name] ?? null;
    }

    public function post(string $name): ?string
    {
        return $this->post[$name] ?? null;
    }

    public function isGet(): bool
    {
        return $this->server['REQUEST_METHOD'] === 'GET';
    }

    public function uri(): string
    {
        return $this->server['REQUEST_URI'];
    }
}
