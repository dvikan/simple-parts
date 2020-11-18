<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use PDO;

class Migrator
{
    private $migrationFolder;
    private $cacheFolder;
    private $pdo;

    public function __construct(
        PDO $pdo,
        string $migrationFolder = './migrations',
        string $cacheFolder = './var'
    ) {
        $this->migrationFolder = $migrationFolder;
        $this->cacheFolder = $cacheFolder;
        $this->pdo = $pdo;
    }

    public function migrate(): void
    {
        if (!is_dir($this->migrationFolder)) {
            printf("Migrations folder not found: %s\n", $this->migrationFolder);
            exit(1);
        }

        if (!is_dir($this->cacheFolder)) {
            printf("Cache folder not found: %s\n", $this->cacheFolder);
            exit(1);
        }

        $storage = new JsonFile($this->cacheFolder . '/migrator.json');

        $pastMigrations = $storage->getContents();

        $messages = [];

        foreach (glob($this->migrationFolder . '/*.sql') as $migration) {
            if (in_array($migration, $pastMigrations)) {
                continue;
            }

            $sql = file_get_contents($migration);

            if ($sql === false) {
                throw new SimpleException(sprintf('Unable to read contents of "%s"', $migration));
            }

            $result = $this->pdo->exec($sql);

            if ($result === false) {
                sprintf("pdo error: %s\n", $this->pdo->errorInfo()[2]);
                exit(1);
            }

            $messages[] = "Running $migration";
            $pastMigrations[] = $migration;
            $storage->putContents($pastMigrations);
        }


        if ($messages === []) {
            exit(0);
        }

        print implode(PHP_EOL, $messages) . PHP_EOL;
    }
}
