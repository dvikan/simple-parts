<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class CliHandler
{
    public function handle(array $record): void
    {
        try {
            $json = Json::encode($record['context']);
        } catch (SimpleException $e) {
            $json = sprintf('Unable to json encode context: "%s"', $e->getMessage());
        }

        if (PHP_SAPI === 'cli-server') {
            print '<pre>';
        }

        $result = sprintf(
            "[%s] %s.%s %s %s",
            $record['created_at']->format('Y-m-d H:i:s'),
            $record['name'],
            $record['level_name'],
            $record['message'],
            $json
        );

        print "$result\n";

        if (PHP_SAPI === 'cli-server') {
            print '</pre>';
        }
    }
}
