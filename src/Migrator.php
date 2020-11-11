<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use PDO;

class Migrator
{
    private $migrations;
    private $cacheFolder;
    private $pdo;

    public function __construct(
        PDO $pdo,
        string $migrations = './migrations',
        string $cacheFolder = './var'
    ) {
        $this->migrations = $migrations;
        $this->cacheFolder = $cacheFolder;
        $this->pdo = $pdo;
    }

    public function migrate(): void
    {
        $this->guardAgainstNonExistingFolders();

        $cache = JsonFile::fromFile(sprintf("%s/migrator.json", $this->cacheFolder));

        $migrations = $cache->read();

        $messages = [];

        foreach (glob($this->migrations . '/*.sql') as $migration) {
            if (in_array($migration, $migrations)) {
                continue;
            }

            $result = $this->pdo->exec(file_get_contents($migration));

            if ($result === false) {
                exit(sprintf("pdo error: %s\n", $this->pdo->errorInfo()[2]));
            }

            $messages[] = "Running $migration";
            $migrations[] = $migration;
            $cache->write($migrations);
        }


        if ($messages === []) {
            exit(0);
        }

        print implode(PHP_EOL, $messages) . PHP_EOL;
    }

    private function guardAgainstNonExistingFolders(): void
    {
        if (!is_dir($this->migrations)) {
            exit(sprintf("Migrations folder not found: %s\n", $this->migrations));
        }

        if (!is_dir($this->cacheFolder)) {
            exit(sprintf("Cache folder not found: %s\n", $this->cacheFolder));
        }
    }
}
