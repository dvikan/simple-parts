<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

use PDO;

final class SqliteCache implements Cache
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

        $this->pdo->query(<<<'SQL'
CREATE TABLE if not exists cache (
    id integer primary key autoincrement,
    key text,
    value text,
    expiration text
)
SQL
);
    }

    public function set(string $key, $value = true, int $ttl = 0): void
    {
        if ($ttl === 0) {
            $expiration = 0;
        } else {
            $expiration = time() + $ttl;
        }

        $stmt = $this->pdo->prepare('select key from cache where key=?');
        $stmt->execute([$key]);
        $result = $stmt->fetch();

        if ($result) {
            $stmt = $this->pdo->prepare('update cache set value = ?, expiration = ? where key = ?');
            $stmt->execute([Json::encode($value), $expiration, $key]);
        } else {
            $stmt = $this->pdo->prepare('insert into cache (key, value, expiration) values (?, ?, ?)');
            $stmt->execute([$key, Json::encode($value), $expiration]);
        }
    }

    public function get(string $key, $default = null)
    {
        $stmt = $this->pdo->prepare('select value, expiration from cache where key=?');
        $stmt->execute([$key]);
        $result = $stmt->fetch();

        if (!$result) {
            return $default;
        }

        $expiration = (int) $result->expiration;

        if ($expiration === 0) {
            return Json::decode($result->value);
        }

        if ($expiration < time()) {
            $stmt = $this->pdo->prepare('delete from cache where key = ?');
            $stmt->execute([$key]);
            return $default;
        }

        return Json::decode($result->value);
    }

    public function delete(string $key): void
    {
        $stmt = $this->pdo->prepare('delete from cache where key=?');

        $stmt->execute([$key]);
    }

    public function clear(): void
    {
        $this->pdo->query('delete from cache');
    }
}