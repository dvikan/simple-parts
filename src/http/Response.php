<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Response
{
    private $body;
    private $code;
    private $headers;

    public function __construct(
        string $body = '',
        int $code = HttpClient::OK,
        array $headers = []
    ) {
        $this->body = $body;
        $this->code = $code;
        $this->headers = $headers;

        if (! isset(HttpClient::STATUS_LINES[$code])) {
            // Consider dropping this
            throw new SimpleException(sprintf('Invalid status code "%s"', $code));
        }
    }

    public function body(): string
    {
        return $this->body;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function json(): array
    {
        return Json::decode($this->body);
    }

    public function statusLine(): string
    {
        return HttpClient::STATUS_LINES[$this->code];
    }

    public function ok(): bool
    {
        return $this->code === HttpClient::OK;
    }

    public function isRedirect()
    {
        return in_array($this->code, [
            HttpClient::MOVED_PERMANENTLY,
            HttpClient::FOUND,
            HttpClient::SEE_OTHER,
        ]);
    }

    // consider cloning self in with* methods
    public function withCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function withJson(array $data): self
    {
        $this->body = Json::encode($data);
        $this->headers['content-type'] = 'application/json';
        return $this;
    }

    public function withRedirect(string $url): self
    {
        // todo: perhaps validate url
        $this->code = HttpClient::FOUND;
        $this->headers['location'] = $url;
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
