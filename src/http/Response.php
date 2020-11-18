<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class Response
{
    private $body;
    private $code;
    private $headers;

    public function __construct(string $body = '', int $code = 200, array $headers = [])
    {
        $this->body = $body;
        $this->code = $code;
        $this->headers = $headers;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function json(): array
    {
        return Json::decode($this->body);
    }

    public function code(): int
    {
        return $this->code;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function isOk(): bool
    {
        return $this->code === 200;
    }

    public function withJson(array $data): self
    {
        $this->body = Json::encode($data);
        $this->headers['Content-type'] = 'application/json';
        return $this;
    }

    public function send()
    {
        http_response_code($this->code);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        print $this->body;
    }
}
