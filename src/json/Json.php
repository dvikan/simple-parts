<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Json
{
    public static function encode($object, int $flags = 0): string
    {
        try {
            return json_encode(
                $object,
                JSON_THROW_ON_ERROR
                | JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
                | $flags
            );
        } catch (\JsonException $e) {
            throw new SimpleException('json_encode(): ' . $e->getMessage(), $e->getCode());
        }
    }

    public static function decode(string $json, bool $assoc = true)
    {
        try {
            return json_decode($json, $assoc, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new SimpleException('json_decode(): ' . $e->getMessage(), $e->getCode());
        }
    }
}
