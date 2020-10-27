<?php

declare(strict_types=1);

namespace dvikan\SimpleParts;

class Request
{
    private $get;
    private $post;
    private $server;

    public static function fromGlobals(): Request
    {
        $request = new Request;
        $request->get = $_GET;
        $request->post = $_POST;
        $request->server = $_SERVER;
        return $request;
    }

    public static function fromArrays(
        array $get = null,
        array $post = null,
        array $server = null
    ): Request {
        $request = new Request;
        $request->get = $get;
        $request->post = $post;
        $request->server = $server;
        return $request;
    }

    public function get(string $name)
    {
        return $this->get[$name] ?? null;
    }

    public function post(string $name)
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
