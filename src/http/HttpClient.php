<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class HttpClient
{
    public const GET    = 'GET';
    public const POST   = 'POST';

    public const OK                     = 200;
    public const MOVED_PERMANENTLY      = 301;
    public const FOUND                  = 302;
    public const SEE_OTHER              = 303;
    public const BAD_REQUEST            = 400;
    public const UNAUTHORIZED           = 401;
    public const INTERNAL_SERVER_ERROR  = 500;

    public const STATUS_LINES = [
        self::OK                    => '200 OK',
        self::MOVED_PERMANENTLY     => '301 Moved Permanently',
        self::FOUND                 => '302 Found',
        self::SEE_OTHER             => '303 See Other',
        self::BAD_REQUEST           => '400 Bad Request',
        self::UNAUTHORIZED          => '401 Unauthorized',
        self::INTERNAL_SERVER_ERROR => '500 Internal Server Error',
    ];

    public const BODY               = 'body';
    public const USERAGENT          = 'useragent';
    public const CONNECT_TIMEOUT    = 'connect_timeout';
    public const TIMEOUT            = 'timeout';
    public const CLIENT_ID          = 'client_id';
    public const AUTH_BEARER        = 'auth_bearer';
    //public const AUTH_BASIC         = 'auth_basic';

    public const LOCATION           = 'location';

    private const DEFAULT_OPTIONS = [
        self::USERAGENT         => 'HttpClient',
        self::CONNECT_TIMEOUT   => 10,
        self::TIMEOUT           => 10,
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
        return $this->request(self::GET, $url, $options);
    }

    public function post(string $url, array $options = []): Response
    {
        return $this->request(self::POST, $url, $options);
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

        if ($method === self::POST) {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->options[self::BODY]);
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

        if ($response->isOk()) {
            return $response;
        }

        if ($response->isRedirect()) {
            $foundUrl = $response->headers()[self::LOCATION];
            return $this->request($method, $foundUrl, $options); // warning: infinite loop
        }

        throw new SimpleException('The HTTP response code was not 2xx', $response->code());
    }

    public function __destruct()
    {
        if (isset($this->ch)) {
            curl_close($this->ch);
        }
    }
}
