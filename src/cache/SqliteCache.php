<?php
declare(strict_types=1);

namespace dvikan\SimpleParts;

use PDO;

final class SqliteCache implements Cache
{
    private $pdo;
    private $name;

    public function __construct(string $name = 'cache', PDO $pdo = null)
    {
        $this->name = $name;
        $this->pdo = $pdo ?? new PDO('sqlite:cache.db');

        $this->pdo->query(sprintf('create table if not exists %s (id integer primary key, key text, value text, expiration text)', $this->name));
    }

    public function set(string $key, $value = true, int $ttl = 0): void
    {
        $this->delete($key);

        if ($ttl === 0) {
            $expiration = 0;
        } else {
            $expiration = time() + $ttl;
        }

        $stmt = $this->pdo->prepare(sprintf('insert into %s (key, value, expiration) values (?, ?, ?)', $this->name));
        $stmt->execute([$key, Json::encode($value), $expiration]);
    }

    public function get(string $key, $default = null)
    {
        $stmt = $this->pdo->prepare(sprintf('select value, expiration from %s where key = ?', $this->name));
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$result) {
            return $default;
        }

        $expiration = (int) $result->expiration;

        if ($expiration === 0 || $expiration >= time()) {
            return Json::decode($result->value);
        }

        $this->delete($key);
        return $default;
    }

    public function delete(string $key): void
    {
        $stmt = $this->pdo->prepare(sprintf('delete from %s where key = ?', $this->name));
        $stmt->execute([$key]);
    }

    public function clear(): void
    {
        $this->pdo->query(sprintf('delete from %s', $this->name));
    }
}