<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Response
{
    private $body;
    private $code;
    private $headers;

    public function __construct(string $body = '', int $code = Http::OK, array $headers = [])
    {
        $this->body = $body;
        $this->code = $code;
        $this->headers = $headers; // todo: canonicalize
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
        return Http::STATUS_LINES[$this->code] ?? 'Unknown status code: ' . $this->code;
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
            ->withHeader(Http::CONTENT_TYPE, Http::APPLICATION_JSON)
            ->withBody(Json::encode($data, JSON_PRETTY_PRINT));
    }

    public function ok(): bool
    {
        return in_array($this->code, [Http::OK, Http::CREATED]);
    }

    public function redirect(): bool
    {
        return in_array($this->code, [Http::MOVED_PERMANENTLY, Http::FOUND, Http::SEE_OTHER]);
    }

    public function withRedirect(string $location): self
    {
        $response = clone $this;
        return $response
            ->withCode(Http::SEE_OTHER)
            ->withHeader(Http::LOCATION, $location);
    }

    public function send(): void
    {
        $response = clone $this;

        $response = $response->withHeader(Http::CONTENT_LENGTH, (string) \strlen($this->body));
        
        if (headers_sent()) {
            print 'Headers already sent';
        } else {
            http_response_code($response->code());

            foreach ($response->headers() as $key => $value) {
                header(sprintf('%s: %s', $key, $value));
            }
        }

        print $response->body();
    }
}
