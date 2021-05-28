<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class CachingHttpClient implements HttpClient
{
    private $httpClient;
    private $cache;
    private $ttl;

    public function __construct(
        HttpClient $httpClient,
        Cache $cache,
        int $ttl = 60 * 15
    ) {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    public function head(string $url, array $config = []): Response
    {
        throw new \Exception('not implemented');
    }

    public function request(string $method, string $url, array $config = []): Response
    {
        throw new \Exception('not implemented');
    }

    public function post(string $url, array $config = []): Response
    {
        throw new \Exception('not implemented');
    }

    public function get(string $url, array $config = []): Response
    {
        $isLocallyCachedKey = hash('sha256', 'http_is_locally_cached_' . $url);
        $cachedResponseKey  = hash('sha256', 'http_cached_response_' . $url);

        $isLocallyCached = $this->cache->get($isLocallyCachedKey);
        $cachedResponse = $this->cache->get($cachedResponseKey);

        if ($isLocallyCached) {
            return (new Response)
                ->withCode($cachedResponse[0])
                ->withHeaders($cachedResponse[1])
                ->withHeader('cache-hit', 'local hit')
                ->withBody($cachedResponse[2])
            ;
        }

        $headers = [];

        if ($cachedResponse) {
            if (isset($cachedResponse[1][Http::LAST_MODIFIED])) {
                $headers[Http::IF_MODIFIED_SINCE] = $cachedResponse[1][Http::LAST_MODIFIED];
            }

            if (isset($cachedResponse[1][Http::ETAG])) {
                $headers[Http::IF_NONE_MATCH] = $cachedResponse[1][Http::ETAG];
            }
        }

        try {
            $response = $this->httpClient->get($url, ['headers' => $headers]);
        } catch (SimpleException $e) {
            if ($e->getCode() === Http::NOT_MODIFIED) {
                $this->cache->set($isLocallyCachedKey, true, $this->ttl);
                return (new Response)
                    ->withCode($cachedResponse[0])
                    ->withHeaders($cachedResponse[1])
                    ->withHeader('cache-hit', 'not modified')
                    ->withBody($cachedResponse[2])
                ;
            }
            throw $e;
        }

        $this->cache->set($isLocallyCachedKey, true, $this->ttl);
        $this->cache->set($cachedResponseKey, [$response->getCode(), $response->getHeaders(), $response->getBody()]);

        return $response;
    }
}
