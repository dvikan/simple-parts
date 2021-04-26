<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTime;
use Exception;
use SimpleXMLElement;

final class RssClient
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /** @var HttpClient */
    private $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function fromUrl(string $url): array
    {
        $response = $this->client->get($this->prepareUrl($url));

        return $this->fromXml($response->body());
    }

    private function fromFile(string $fileName): array
    {
        // todo: TextFile
        $xml = file_get_contents($fileName);

        if ($xml === false) {
            throw new SimpleException('file_get_contents()');
        }

        return self::fromXml($xml);
    }

    private function fromXml(string $xml): array
    {
        $previous = libxml_use_internal_errors(true);

        try {
            $xml = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            throw new SimpleException(sprintf('Unable to parse xml: '. $e->getMessage()));
        } finally {
            libxml_use_internal_errors($previous);
        }

        if (isset($xml->channel)) {
            $feed = $this->fromRss($xml);
        } else {
            $feed = $this->fromAtom($xml);
        }

        usort($feed['items'], function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return $feed;
    }

    private function fromRss(SimpleXMLElement $xml)
    {
        $channel = [
            'title'         => (string) $xml->channel->title,
            'link'          => (string) $xml->channel->link,
            'items'         => [],
        ];

        foreach ($xml->channel->item as $item) {
            $_item = [
                'title'         => (string) $item->title,
                'link'          => null,
                'guid'          => (string) $item->guid,
                'date'          => DateTime::createFromFormat(DATE_RSS, (string) $item->pubDate)->format(self::DATE_FORMAT),
                'description'   => (string) $item->description,
            ];

            // Not all items has a link
            if (isset($item->link)) {
                $_item['link'] = (string) $item->link;
            }

            $channel['items'][] = $_item;
        }

        return $channel;
    }

    private function fromAtom(SimpleXMLElement $xml)
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
                'guid'          => (string) $entry->id,
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


    private function prepareUrl($url)
    {
        if (preg_match('#^https://www.youtube.com/channel/(\w+)$#', $url, $matches)) {
            return 'https://www.youtube.com/feeds/videos.xml?channel_id=' . $matches[1];
        }

        if (preg_match('#^https://www.youtube.com/user/(\w+)$#', $url, $matches)) {
            return 'https://www.youtube.com/feeds/videos.xml?user=' . $matches[1];
        }

        if (preg_match('#^https://www.reddit.com/r/(\w+)$#', $url)) {
            return $url . '.rss';
        }

        if (preg_match('#^https://play.acast.com/s/([a-z]+)$#', $url, $matches) === 1) {
            return 'https://rss.acast.com/' . $matches[1];
        }

        return $url;
    }
}
