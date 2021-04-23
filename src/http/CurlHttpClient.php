<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class CurlHttpClient implements HttpClient
{
    private const DEFAULT_OPTIONS = [
        HttpClient::USERAGENT         => 'HttpClient',
        HttpClient::CONNECT_TIMEOUT   => 10,
        HttpClient::TIMEOUT           => 10,
        'headers' => [
        ]
    ];

    private $ch;
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = array_merge(self::DEFAULT_OPTIONS, $options);

        $this->ch = curl_init();

        if ($this->ch === false) {
            throw new SimpleException();
        }
    }

    public function get(string $url, array $options = []): Response
    {
        return $this->request(HttpClient::GET, $url, $options);
    }

    public function post(string $url, array $options = []): Response
    {
        return $this->request(HttpClient::POST, $url, $options);
    }

    private function request(string $method, string $url, array $options)
    {
        $this->options = array_merge($this->options, $options);

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->options[self::USERAGENT]);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->options[self::CONNECT_TIMEOUT]);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->options[self::TIMEOUT]);
        curl_setopt($this->ch, CURLOPT_HEADER, false);

        if ($method === HttpClient::POST) {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->options[HttpClient::BODY]);
        }

        $headers = $this->options['headers'] ?? [];

        if (isset($this->options[self::AUTH_BEARER])) {
            $headers[] = sprintf('Authorization: Bearer %s', $this->options[self::AUTH_BEARER]);
        }

        if (isset($this->options[self::CLIENT_ID])) {
            $headers[] = sprintf('client-id: %s', $this->options[self::CLIENT_ID]);
        }

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        $headers = [];
        curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, function ($ch, $rawHeader) use (&$headers) {
            $len = strlen($rawHeader);
            $header = explode(':', $rawHeader); // todo: regex

            if (count($header) === 1) {
                // Discard the status line and malformed headers
                return $len;
            }

            $name = strtolower(trim($header[0]));
            $value = trim(implode(':', array_slice($header, 1)));
            $headers[$name] = $value;

            return $len;
        });

        $body = curl_exec($this->ch);

        if ($body === false) {
            throw new SimpleException(curl_error($this->ch));
        }

        $statusCode = curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE);

        $response = new Response($body, $statusCode, $headers);

        if ($response->ok()) {
            return $response;
        }

        if ($response->isRedirect()) {
            $foundUrl = $response->headers()[HttpClient::LOCATION];

            // warning: infinite loop
            return $this->request($method, $foundUrl, $options);
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
