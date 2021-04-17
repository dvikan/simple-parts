<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class NullHttpClient implements HttpClient
{
    public function get(string $url, array $options = []): Response
    {
        return response();
    }

    public function post(string $url, array $vars = [], array $options = []): Response
    {
        return response();
    }
}
