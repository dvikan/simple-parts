<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class Request
{
    private $get;
    private $post;
    private $server;

    public static function fromGlobals(): Request
    {
        return new Request($_GET, $_POST, $_SERVER);
    }

    private function __construct(array $get, array $post, array $server)
    {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
    }

    /**
     * Get string POST var. Otherwise null.
     */
    public function post(string $name): string
    {
        if (! isset($this->post[$name])) {
            return '';
        }

        if (! is_string($this->post[$name])) {
            return '';
        }

        return $this->post[$name];
    }

    /**
     * Get array POST var
     */
    public function postArray(string $name): array
    {
        if (! isset($this->post[$name])) {
            return [];
        }

        if (! is_array($this->post[$name])) {
            return [];
        }

        return $this->post[$name];
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
