<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class YahooFinanceClient
{
    private const API = 'https://query1.finance.yahoo.com/v7/finance';

    /** @var HttpClient  */
    private $client;

    public function __construct(HttpClient $client = null)
    {
        $this->client = $client ?? new CurlHttpClient();
    }

    public function quote(array $symbols)
    {
        $url = sprintf('%s/quote?symbols=%s', self::API, implode(',', $symbols));

        $response = $this->client->get($url);

        $quote = $response->json();

        return $quote['quoteResponse']['result'];
    }
}
