<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class CliHandler implements Handler
{
    public function handle(array $record): void
    {
        try {
            $json = Json::encode($record['context'], JSON_PRETTY_PRINT);
        } catch (SimpleException $e) {
            $json = sprintf('Unable to json encode context: "%s"', $e->getMessage());
        }

        if (PHP_SAPI === 'cli-server' || PHP_SAPI === 'fpm-fcgi') {
            //http_response_code(500);
            if (ob_get_length() > 0) {
                ob_end_clean();
            }
            print '<pre>';
        }

        $result = sprintf('%s.%s %s %s', $record['name'], $record['level_name'], $record['message'], $json);

        print "$result\n";
    }
}
