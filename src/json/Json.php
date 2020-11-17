<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

class Json
{
    public static function encode($object): string
    {
        return json_encode(
            $object,
            JSON_THROW_ON_ERROR
            | JSON_PRETTY_PRINT
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
        );
    }

    public static function decode(string $json)
    {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}
