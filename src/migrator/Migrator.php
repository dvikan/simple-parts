<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use PDO;

final class Migrator
{
    private $pdo;
    private $migrationsFolder;
    private $cacheFolder;

    public function __construct(
        PDO $pdo,
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
            throw new SimpleException(sprintf('Zero migrations found in "%s"', $this->migrationsFolder);
        }

        $cache = new Cache(new TextFile($this->cacheFolder . '/migrations.json'));

        foreach ($migrations as $migration) {
            $fileName = basename($migration);

            if ($cache->get($fileName, false)) {
                continue;
            }

            // todo: textfile
            $sql = file_get_contents($migration);

            if ($this->pdo->exec($sql) === false) {
                throw new SimpleException(sprintf('%s: %s', $fileName, $this->pdo->errorInfo()[2]));
            }

            $result[] = sprintf('Migrated "%s"', $fileName);

            $cache->set($fileName);
        }

        return $result;
    }
}
