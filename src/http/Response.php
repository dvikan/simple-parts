<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use function header;
use function http_response_code;

final class Response
{
    private $body;
    private $code;
    private $headers;

    public function __construct(string $body = '', int $code = HttpClient::OK, array $headers = [])
    {
        $this->body = $body;
        $this->code = $code;
        $this->headers = $headers;

        if (! isset(HttpClient::STATUS_LINES[$code])) {
            throw new SimpleException(sprintf('Illegal status code "%s"', $code));
        }
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

    public function statusLine(): string
    {
        return HttpClient::STATUS_LINES[$this->code];
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function ok(): bool
    {
        return $this->code === HttpClient::OK;
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
