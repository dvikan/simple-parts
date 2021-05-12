<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

final class CliHandler implements Handler
{
    public function handle(array $record): void
    {
        try {
            $json = Json::encode($record['context']);
        } catch (SimpleException $e) {
            $json = sprintf('Unable to json encode context: "%s"', $e->getMessage());
        }



        if (PHP_SAPI === 'cli-server') {
            // todo: see how monolog does this
            if (ob_get_length() > 0) {
                ob_end_clean();
            }
            print '<pre style="font-size: 16px">';
        }

        $result = sprintf(
            "[%s] %s.%s %s %s",
            $record['created_at']->format('Y-m-d H:i:s'),
            $record['name'],
            $record['level_name'],
            $record['message'],
            $json
        );

        // todo: escape for browser and/or escape for cli
        print ("$result\n");

        if (PHP_SAPI === 'cli-server') {
            print '</pre>';
        }
    }
}
