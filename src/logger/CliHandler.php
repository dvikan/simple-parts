<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class CliHandler
{
    public function handle(array $record): void
    {
        try {
            $context = Json::encode($record['context']);
        } catch (SimpleException $e) {
            $context = 'Unable to encode context as json';
        }

        if (in_array(PHP_SAPI, ['cli-server'])) {
            print '<pre>';
        }

        printf(
            "[%s] %s.%s %s %s\n",
            $record['datetime']->format('Y-m-d H:i:s'),
            $record['channel'],
            $record['level_name'],
            $record['message'],
            $context
        );

        if (in_array(PHP_SAPI, ['cli-server'])) {
            print '</pre>';
        }
    }
}
