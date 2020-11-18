<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class HttpClient
{
    const USERAGENT         = 'useragent';
    const CONNECT_TIMEOUT   = 'connect_timeout';
    const TIMEOUT           = 'timeout';
    const CLIENT_ID         = 'client_id';
    const AUTH_BEARER       = 'auth_bearer';

    const OPTIONS = [
        self::USERAGENT         => 'Curl',
        self::CONNECT_TIMEOUT   => 10,
        self::TIMEOUT           => 10,
        self::CLIENT_ID         => null,
        self::AUTH_BEARER       => null,
    ];

    private $options;
    private $ch;

    public function __construct(array $options = [])
    {
        $this->options = array_merge(self::OPTIONS, $options);
        $this->ch = null;
    }

    public function get(string $url): Response
    {
        if (!isset($this->ch)) {
            $this->ch = $this->createCurlHandle();
        }

        curl_setopt($this->ch, CURLOPT_URL, $url);

        return $this->execute();
    }

    public function post(string $url, array $vars = []): Response
    {
        if ($this->ch === null) {
            $this->ch = $this->createCurlHandle();
        }

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $vars);

        return $this->execute();
    }

    private function createCurlHandle()
    {
        $ch = curl_init();

        if ($ch === false) {
            throw new SimpleException('Unable to create curl handle');
        }

        curl_setopt($ch, CURLOPT_USERAGENT, $this->options[self::USERAGENT]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->options[self::CONNECT_TIMEOUT]);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->options[self::TIMEOUT]);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $headers = [];

        if (isset($this->options[self::AUTH_BEARER])) {
            $headers[] = sprintf('Authorization: Bearer %s', $this->options[self::AUTH_BEARER]);
        }

        if (isset($this->options[self::CLIENT_ID])) {
            $headers[] = sprintf('client-id: %s', $this->options[self::CLIENT_ID]);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        return $ch;
    }

    private function execute(): Response
    {
        $headers = [];
        curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$headers) {
            $len = strlen($header);
            $header = explode(':', $header);

            if (count($header) !== 2) {
                return $len;
            }

            $name = strtolower(trim($header[0]));
            $value = trim($header[1]);
            $headers[$name] = $value;

            return $len;
        });

        $body = curl_exec($this->ch);

        if ($body === false) {
            throw new SimpleException(sprintf('Curl error: %s', curl_error($this->ch)));
        }

        $code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $response = new Response($body, $code, $headers);

        if (!$response->isOk()) {
            throw new SimpleException('The response was not ok', $code);
        }

        return $response;
    }

    public function __destruct()
    {
        if (isset($this->ch)) {
            curl_close($this->ch);
        }
    }
}
