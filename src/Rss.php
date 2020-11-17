<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTime;
use SimpleXMLElement;

final class Rss
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    public static function fromUrl(string $url): array
    {
        $httpClient = HttpClient::fromOptions();

        $response = $httpClient->get($url);

        return self::fromXml($response->body());
    }

    public static function fromFile(string $file): array
    {
        $xml = file_get_contents($file);
        if ($xml === false) {
            throw new SimpleException();
        }
        return self::fromXml($xml);
    }

    public static function fromXml(string $xml): array
    {
        $xml = new SimpleXMLElement($xml);

        if (isset($xml->channel)) {
            $feed = self::fromRss($xml);
        } else {
            $feed = self::fromAtom($xml);
        }

        usort($feed['items'], function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return $feed;
    }

    private static function fromRss($xml)
    {
        $channel = [
            'title'         => (string) $xml->channel->title,
            'link'          => (string) $xml->channel->link,
            'items'         => [],
        ];

        foreach ($xml->channel->item as $item) {
            $_item = [
                'title'         => (string) $item->title,
                'link'          => (string) $item->link,
                'date'          => DateTime::createFromFormat(DATE_RSS, (string) $item->pubDate)->format(self::DATE_FORMAT),
                'description'   => (string) $item->description,
            ];

            $channel['items'][] = $_item;
        }

        return $channel;
    }

    private static function fromAtom(SimpleXMLElement $xml)
    {
        $feed = [
            'title'         => (string) $xml->title,
            'link'          => (string) $xml->link,
            'items'         => [],
        ];

        foreach ($xml->entry as $entry) {
            $item = [
                'title'         => (string) $entry->title,
                'link'          => (string) $entry->link['href'],
                'description'   => (string) $entry->content,
            ];

            if ($entry->published) {
                $item['date'] = DateTime::createFromFormat(DATE_ATOM, (string) $entry->published)->format(self::DATE_FORMAT);
            } else {
                $item['date'] = DateTime::createFromFormat(DATE_ATOM, (string) $entry->updated)->format(self::DATE_FORMAT);
            }

            $feed['items'][] = $item;
        }

        return $feed;
    }
}
