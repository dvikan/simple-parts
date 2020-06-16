<?php declare(strict_types=1);

namespace StaticParts;

use PDO;

class Migrator
{
    private $folder;
    private $cache;
    private $dsn;

    public function __construct(string $dsn, string $folder, string $cache)
    {
        if (! is_dir($folder)) {
            mkdir($folder);
        }

        if (! file_exists($cache)) {
            touch($cache);
        }

        $this->folder = $folder;
        $this->cache = $cache;
        $this->dsn = $dsn;
    }

    public function run()
    {
        $pdo = new PDO($this->dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $doneMigrations = array_map('trim', file($this->cache));

        foreach(glob($this->folder . '/*.sql') as $migration) {
            if (in_array($migration, $doneMigrations)) {
                continue;
            }

            print "Running `$migration`\n";

            $pdo->exec(file_get_contents($migration));

            $fp = fopen($this->cache, 'a');
            fwrite($fp, "$migration\n");
            fclose($fp);
        }
    }
}
