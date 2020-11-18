<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

interface HttpClient
{
    public function get(string $url): Response;

    public function post(string $url, array $vars = []): Response;
}
