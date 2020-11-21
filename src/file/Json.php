<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Json
{
    /**
     * @throws SimpleException
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
            throw new SimpleException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws SimpleException
     * @return mixed
     */
    public static function decode(string $json)
    {
        try {
            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new SimpleException($e->getMessage(), $e->getCode());
        }
    }
}
