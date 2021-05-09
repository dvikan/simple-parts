<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Response
{
    private $body;
    private $code;
    private $headers;

    public function __construct(
        string $body = '',
        int $code = Http::OK,
        array $headers = []
    ) {
        $this->body = $body;
        $this->code = $code;
        $this->headers = $headers;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function withCode(int $code): self
    {
        $response = clone $this;

        $response->code = $code;

        return $response;
    }

    public function statusLine(): string
    {
        return Http::STATUS_LINES[$this->code] ?? 'unknown status line: ' . $this->code;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function withBody(string $body): self
    {
        $response = clone $this;

        $response->body = $body;

        return $response;
    }

    public function header(string $key, string $default = null): ?string
    {
        return $this->headers[$key] ?? $default;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function withHeader(string $key, string $value): self
    {
        $response = clone $this;

        $response->headers[$key] = $value;

        return $response;
    }

    public function json(): array
    {
        return Json::decode($this->body);
    }

    public function withJson(array $data): self
    {
        $response = clone $this;

        return $response
            ->withHeader(Http::CONTENT_TYPE, 'application/json')
            ->withBody(Json::encode($data));
    }

    public function ok(): bool
    {
        return $this->code === Http::OK;
    }

    public function redirect(): bool
    {
        return in_array($this->code, [
            Http::MOVED_PERMANENTLY,
            Http::FOUND,
            Http::SEE_OTHER,
        ]);
    }

    public function withRedirect(string $location): self
    {
        $response = clone $this;

        return $response
            ->withCode(Http::FOUND)
            ->withHeader(Http::LOCATION, $location);
    }

    public function send(): void
    {
        http_response_code($this->code);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        print $this->body;
    }
}
