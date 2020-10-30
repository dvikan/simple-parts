<?php

declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTimeImmutable;
use SimpleXMLElement;

final class Rss
{
    public static function fromUrl(string $url): array
    {
        $httpClient = new HttpClient();

        $response = $httpClient->get($url);

        return self::fromXml($response->body());
    }

    public static function fromXml(string $xml): array
    {
        $xml = new SimpleXMLElement($xml);

        $channel = [
            'title'         => (string) $xml->channel->title,
            'description'   => (string) $xml->channel->description,
            'items'         => [],
        ];

        foreach($xml->channel->item as $item) {
            $guid = (string) $item->guid;
            $date = new DateTimeImmutable((string)$item->pubDate);

            $item = [
                'title'     => (string) $item->title,
                'category'  => (string) $item->category,
                'link'      => (string) $item->link,
                'date'      => $date->format('Y-m-d H:i:s'),
                'guid'      => $guid,
            ];

            $channel['items'][$guid] = $item;
        }

        return $channel;
    }
}
