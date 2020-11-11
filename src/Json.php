<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class Json
{
    public static function encode($object): string
    {
        $json = json_encode(
            $object,
            JSON_PRETTY_PRINT
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_LINE_TERMINATORS
            | JSON_UNESCAPED_UNICODE
        );

        if($json === false) {
            throw new SimpleException('Unable to encode json');
        }

        return $json;
    }

    public static function decode(string $json)
    {
        $object = json_decode($json, true);

        if($object === null) {
            throw new SimpleException('Unable to decode json');
        }

        return $object;
    }
}
