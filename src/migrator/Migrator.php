<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

final class Migrator
{
    private $pdo;
    private $migrationsFolder;
    private $cacheFolder;

    public function __construct(
        \PDO $pdo,
        string $migrationsFolder = './',
        string $cacheFolder = './'
    ) {
        $this->pdo = $pdo;
        $this->migrationsFolder = $migrationsFolder;
        $this->cacheFolder = $cacheFolder;
    }

    public function migrate(): array
    {
        if (! is_dir($this->migrationsFolder)) {
            throw new SimpleException(sprintf('Not a folder: "%s"', $this->migrationsFolder));
        }

        if (! is_dir($this->cacheFolder)) {
            throw new SimpleException(sprintf('Not a folder: "%s"', $this->cacheFolder));
        }

        $result = [];

        $migrations = glob($this->migrationsFolder . '/*.sql');

        if ($migrations === false) {
            throw new SimpleException(sprintf('Error reading migrations folder: "%s"', $this->migrationsFolder));
        }

        if ($migrations === []) {
            throw new SimpleException(sprintf('The migrations folder is empty: "%s"', $this->migrationsFolder));
        }

        $cache = new FileCache(new TextFile($this->cacheFolder . '/migrations.json'));

        foreach ($migrations as $migration) {
            $file = new TextFile($migration);

            $fileName = basename($migration);

            if ($cache->get($fileName, false)) {
                continue;
            }

            $sql = $file->read();

            if ($this->pdo->exec($sql) === false) {
                throw new SimpleException(sprintf('%s: %s', $fileName, $this->pdo->errorInfo()[2]));
            }

            $result[] = sprintf('Migrated "%s"', $fileName);

            $cache->set($fileName);
        }

        return $result;
    }
}
