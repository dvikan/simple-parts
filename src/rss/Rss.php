<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use DateTime;
use Exception;
use SimpleXMLElement;

final class Rss
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /** @var HttpClient */
    private $client;

    public function __construct(HttpClient $client = null)
    {
        $this->client = $client ?: new CurlHttpClient();
    }

    /**
     * @throws SimpleException
     */
    public function fromUrl(string $url): array
    {
        $xml = $this->client->get($url)->body();
        return $this->fromXml($xml);
    }

    private function fromFile(string $file): array
    {
        $xml = file_get_contents($file);
        if ($xml === false) {
            throw new SimpleException('Call to file_get_contents() failed');
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
                'link'          => (string) $item->link,
                'date'          => DateTime::createFromFormat(DATE_RSS, (string) $item->pubDate)->format(self::DATE_FORMAT),
                'description'   => (string) $item->description,
            ];

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
