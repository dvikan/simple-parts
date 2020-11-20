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
        $symbols = implode(',', $symbols);

        $response = $this->client->get(sprintf("%s/quote?symbols=%s", self::API, $symbols));

        return $response->json()['quoteResponse']['result'];
    }
}
