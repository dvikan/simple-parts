<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class HttpClient
{
    // config
    public const BODY               = 'body';
    public const CONNECT_TIMEOUT    = 'connect_timeout';
    public const TIMEOUT            = 'timeout';
    public const CLIENT_ID          = 'client_id';
    public const AUTH_BEARER        = 'auth_bearer';
    public const HEADERS            = 'headers';
    public const USERAGENT          = 'useragent';

    private const DEFAULT_CONFIG = [
        self::USERAGENT         => 'HttpClient',
        self::CONNECT_TIMEOUT   => 10,
        self::TIMEOUT           => 10,
        self::HEADERS => []
    ];

    private $ch;
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge(self::DEFAULT_CONFIG, $config);

        $this->ch = curl_init();

        if ($this->ch === false) {
            throw new SimpleException();
        }
    }

    public function get(string $url, array $config = []): Response
    {
        return $this->request('GET', $url, $config);
    }

    public function post(string $url, array $config = []): Response
    {
        return $this->request('POST', $url, $config);
    }

    private function request(string $method, string $url, array $config)
    {
        $config = array_merge($this->config, $config);

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $config[self::USERAGENT]);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $config[self::CONNECT_TIMEOUT]);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $config[self::TIMEOUT]);
        curl_setopt($this->ch, CURLOPT_HEADER, false);

        if ($method === 'POST') {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $config[self::BODY]);
        }

        $requestHeaders = $config[self::HEADERS];

        // handle special case headers that need tweaking

        if (isset($config[self::AUTH_BEARER])) {
            $requestHeaders[] = sprintf('Authorization: Bearer %s', $config[self::AUTH_BEARER]);
        }

        if (isset($config[self::CLIENT_ID])) {
            $requestHeaders[] = sprintf('client-id: %s', $config[self::CLIENT_ID]);
        }

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $requestHeaders);

        $responseHeaders = [];

        curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, function ($ch, $rawHeader) use (&$responseHeaders) {
            $len = strlen($rawHeader);
            $header = explode(':', $rawHeader); // todo: needs improvement

            if (count($header) === 1) {
                // Discard the status line and malformed headers
                return $len;
            }

            $key = strtolower(trim($header[0]));
            $value = trim(implode(':', array_slice($header, 1)));

            $responseHeaders[$key] = $value;

            return $len;
        });

        $body = curl_exec($this->ch);

        if ($body === false) {
            throw new SimpleException(curl_error($this->ch));
        }

        $statusCode = curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE);

        $response = new Response($body, $statusCode, $responseHeaders);

        if ($response->ok()) {
            return $response;
        }

        if ($response->redirect()) {
            $location = $response->header(Http::LOCATION);

            return $this->request($method, $location, $config); // todo: prevent infinite loop
        }

        throw new SimpleException($response->statusLine(), $response->code());
    }

    public function __destruct()
    {
        if (isset($this->ch)) {
            curl_close($this->ch);
        }
    }
}
