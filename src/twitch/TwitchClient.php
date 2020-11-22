<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use function http_build_query;

/**
 * https://dev.twitch.tv/docs/api/reference
 */
final class TwitchClient
{
    private $clientId;
    private $clientSecret;
    private $accessToken;

    /** @var HttpClient */
    private $client;

    public function __construct(string $clientId, string $clientSecret, string $accessToken = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken = $accessToken;

        $this->createClient();
    }

    public function accessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Gets information about active streams
     *
     * https://dev.twitch.tv/docs/api/reference#get-streams
     */
    public function streams(array $query = []): array
    {
        $response = $this->client->get(sprintf('https://api.twitch.tv/helix/streams?%s', http_build_query($query)));
        return $response->json();
    }

    private function createClient(): void
    {
        if (isset($this->client)) {
            return;
        }

        if (!isset($this->accessToken)) {
            $token = $this->fetchAccessToken();
            $this->accessToken = $token['access_token'];
        }

        $this->client = new CurlHttpClient([
            HttpClient::CLIENT_ID => $this->clientId,
            HttpClient::AUTH_BEARER => $this->accessToken,
        ]);
    }

    private function fetchAccessToken(): array
    {
        $client = new CurlHttpClient();

        $response = $client->post('https://id.twitch.tv/oauth2/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        return $response->json();

    }
}
