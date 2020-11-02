<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use PDO;

class Migrator
{
    private $folder;
    private $cache;
    private $pdo;

    public function __construct(PDO $pdo, string $folder, string $cache)
    {
        if (! is_dir($folder)) {
            mkdir($folder);
        }

        if (! file_exists($cache)) {
            touch($cache);
        }

        $this->folder = $folder;
        $this->cache = $cache;
        $this->pdo = $pdo;
    }

    public function run()
    {
        $doneMigrations = array_map('trim', file($this->cache));

        foreach (glob($this->folder . '/*.sql') as $migration) {
            if (in_array($migration, $doneMigrations)) {
                continue;
            }

            print "Running `$migration`\n";

            $this->pdo->exec(file_get_contents($migration));

            $fp = fopen($this->cache, 'a');
            fwrite($fp, "$migration\n");
            fclose($fp);
        }
    }
}
