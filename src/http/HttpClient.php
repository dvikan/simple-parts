<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class HttpClient
{
    private $config;

    private const CONFIG = [
        'useragent'         => 'HttpClient',
        'connect_timeout'   => 10,
        'timeout'           => 10,
        'follow_location'   => false,
        'max_redirs'        => 5,
        'auth_bearer'       => null,
        'client_id'         => null,
        'headers' => [],
        'body' => null,
    ];

    private $ch;

    public function __construct(array $config = [])
    {
        $this->config = Config::fromArray(self::CONFIG, $config);

        $this->ch = curl_init();
    }

    public function get(string $url, array $config = []): Response
    {
        return $this->request('GET', $url, $config);
    }

    public function post(string $url, array $config = []): Response
    {
        return $this->request('POST', $url, $config);
    }

    public function request(string $method, string $url, array $requestConfig): Response
    {
        $config = $this->config->merge($requestConfig);

        curl_reset($this->ch);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $config['useragent']);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $config['connect_timeout']);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $config['timeout']);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $config['follow_location']);
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, $config['max_redirs']);

        if ($method === 'POST') {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $config['body']);
        }

        $headers = [];
        if (($config['auth_bearer'])) {
            $headers[] = sprintf('Authorization: Bearer %s', $config['auth_bearer']);
        }

        if (($config['client_id'])) {
            $headers[] = sprintf('client-id: %s', $config['client_id']);
        }

        $requestHeaders = [];
        foreach (array_merge($config['headers'], $headers) as $key => $val) {
            $requestHeaders[] = "$key: $val";
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
            throw new SimpleException(sprintf('"%s" (%s)', curl_error($this->ch), curl_errno($this->ch)));
        }

        $statusCode = curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE);

        $response = new Response($body, $statusCode, $responseHeaders);

        if ($response->ok()) {
            return $response;
        }

        throw new SimpleException($url . ' ' . $response->statusLine(), $response->code());
    }

    public function __destruct()
    {
        if (isset($this->ch)) {
            curl_close($this->ch);
        }
    }
}
