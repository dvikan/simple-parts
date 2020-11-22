<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use function curl_close;
use function curl_init;

final class CurlHttpClient implements HttpClient
{
    private const OPTIONS = [
        HttpClient::USERAGENT         => 'HttpClient',
        HttpClient::CONNECT_TIMEOUT   => 10,
        HttpClient::TIMEOUT           => 10,
    ];

    private $ch;
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = array_merge(self::OPTIONS, $options);
    }

    public function get(string $url): Response
    {
        if (!isset($this->ch)) {
            $this->ch = $this->createCurlHandle();
        }

        return $this->execute($url);
    }

    public function post(string $url, array $vars = []): Response
    {
        if ($this->ch === null) {
            $this->ch = $this->createCurlHandle();
        }

        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $vars);

        return $this->execute($url);
    }

    private function createCurlHandle()
    {
        $ch = curl_init();

        if ($ch === false) {
            throw new SimpleException('curl_init()');
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

    private function execute(string $url): Response
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);

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
            throw new HttpException(sprintf('%s: %s', $url, curl_error($this->ch)));
        }

        $response = response($body, curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE), $headers);

        if ($response->ok()) {
            return $response;
        }

        throw new HttpException(sprintf('%s: %s', $url, $response->statusLine()), $response->code());
    }

    public function __destruct()
    {
        if (isset($this->ch)) {
            curl_close($this->ch);
        }
    }
}