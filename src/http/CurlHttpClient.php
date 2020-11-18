<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class CurlHttpClient implements HttpClient
{
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
            throw new SimpleException('Call to curl_init() failed');
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
            throw new SimpleException(curl_error($this->ch));
        }

        $code = curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE);
        $response = new Response($body, $code, $headers);

        if (! $response->isOk()) {
            throw new SimpleException(sprintf('The response for "%s" was not OK', $url), $code);
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
