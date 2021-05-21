<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

interface HttpClient
{
    public function head(string $url, array $config = []): Response;

    public function get(string $url, array $config = []): Response;

    public function post(string $url, array $config = []): Response;

    public function request(string $method, string $url, array $config): Response;
}