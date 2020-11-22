<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

final class Json
{
    /**
     * @throws JsonException
     */
    public static function encode($object): string
    {
        try {
            return json_encode(
                $object,
                JSON_THROW_ON_ERROR
                | JSON_PRETTY_PRINT
                | JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
            );
        } catch (\JsonException $e) {
            throw new JsonException('json_encode(): ' . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws JsonException
     * @return mixed
     */
    public static function decode(string $json)
    {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new JsonException('json_decode(): ' . $e->getMessage(), $e->getCode());
        }
    }
}
