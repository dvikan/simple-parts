<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

use GlobIterator;
use PDO;

final class Migrator
{
    private $migrations;
    private $pdo;
    private $cache;

    public function __construct(
        PDO $pdo,
        string $migrations = './migrations'
    ) {
        $this->pdo = $pdo;
        $this->migrations = $migrations;
        $this->cache = new SqliteCache('migrations', $this->pdo);
    }

    public function migrate(): array
    {
        if (! is_dir($this->migrations)) {
            throw new SimpleException(sprintf('Not a folder: "%s"', $this->migrations));
        }

        $migrations = [];
        foreach (new GlobIterator($this->migrations . '/*.sql') as $fileInfo) {
            $migrations[] = new TextFile($fileInfo->getPathname());
        }

        if ($migrations === []) {
            throw new SimpleException(sprintf('The migrations folder is empty: "%s"', $this->migrations));
        }

        usort($migrations, function (File $a, File $b) {
            return $a->getFileName() <=> $b->getFileName();
        });

        $result = [];

        foreach ($migrations as $migration) {
            if ($this->cache->get($migration->getFileName())) {
                continue;
            }

            $this->pdo->beginTransaction();
            if ($this->pdo->exec($migration->read()) === false) {
                throw new SimpleException(sprintf('%s: %s', $migration->getFileName(), $this->pdo->errorInfo()[2]));
            }
            $this->pdo->commit();

            $result[] = sprintf('Migrated "%s"', $migration->getFileName());

            $this->cache->set($migration->getFileName());
        }

        return $result;
    }
}
