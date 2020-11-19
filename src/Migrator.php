<?php declare(strict_types=1);

namespace dvikan\SimpleParts;

use PDO;

class Migrator
{
    private $pdo;
    private $folder;
    private $cache;

    public function __construct(PDO $pdo, string $folder) {
        $this->pdo = $pdo;
        $this->folder = rtrim($folder, '/');

        if (! is_dir($this->folder)) {
            throw new SimpleException(sprintf('Not a folder: "%s"', $this->folder));
        }

        $this->cache = new FileCache($this->folder . '/.migrations.json');
    }

    /**
     * @throws SimpleException
     */
    public function migrate(): array
    {
        $messages = [];

        foreach (glob($this->folder . '/*.sql') as $migration) {
            $filename = basename($migration);
            $sql = guard(file_get_contents($migration));

            if ($this->cache->has($filename)) {
                continue;
            }

            if ($this->pdo->exec($sql) === false) {
                throw new SimpleException(sprintf('"%s": %s', $filename, $this->pdo->errorInfo()[2]));
            }

            $messages[] = sprintf('Migrated "%s"', $filename);

            $this->cache->set($filename, true);
        }

        return $messages;
    }
}
