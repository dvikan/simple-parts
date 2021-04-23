<?php declare(strict_types=1);

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
        return new Request($_GET, $_POST, $_SERVER);
    }

    public function get(string $key): ?string
    {
        if (! isset($this->get[$key])) {
            throw new SimpleException('Unknown key');
        }

        return $this->get[$key];
    }

    public function post(string $key): ?string
    {
        if (! isset($this->get[$key])) {
            throw new SimpleException('Unknown key');
        }

        return $this->post[$key];
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
