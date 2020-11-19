<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class NullHttpClient implements HttpClient
{
    public function get(string $url): Response
    {
        return response();
    }

    public function post(string $url, array $vars = []): Response
    {
        return response();
    }
}
