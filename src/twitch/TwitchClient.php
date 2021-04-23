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

    public function __construct(string $clientId, string $clientSecret, string $accessToken = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->accessToken = $accessToken;
    }

    /**
     * Gets information about active streams
     *
     * https://dev.twitch.tv/docs/api/reference#get-streams
     */
    public function streams(array $query = []): array
    {
        if ($this->accessToken === null) {
            $client = new CurlHttpClient();

            $response = $client->post('https://id.twitch.tv/oauth2/token', [
                'body' => [
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type'    => 'client_credentials',
                ]
            ]);

            $token = $response->json();

            $this->accessToken = $token['access_token'];
        }

        $client = new CurlHttpClient([
            HttpClient::CLIENT_ID   => $this->clientId,
            HttpClient::AUTH_BEARER => $this->accessToken,
        ]);
        $response = $client->get(sprintf('https://api.twitch.tv/helix/streams?%s', http_build_query($query)));

        return $response->json();
    }

    public function accessToken(): string
    {
        return $this->accessToken;
    }
}
