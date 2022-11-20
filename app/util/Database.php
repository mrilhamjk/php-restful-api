<?php

namespace App\Util;

use PDO;
use PDOStatement;
use PDOException;
use App\Config\Config;

class Database
{
    private PDO $dbh;
    private PDOStatement $stmt;

    public function __construct()
    {
        try {
            $db_host = Config::DB_HOST;
            $db_user = Config::DB_USER;
            $db_pass = Config::DB_PASS;
            $db_name = Config::DB_NAME;
            $dsn = "mysql:host={$db_host};dbname={$db_name}";
            $this->dbh = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function query(string $query): void
    {
        $this->stmt = $this->dbh->prepare($query);
    }

    public function bind(
        int|string $param,
        mixed $value,
        int $type = null
    ): void {
        if ($type === null) {
            switch (true) {
                case is_int($value):
                case is_float($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }
        }
        $this->stmt->bindValue(
            $param,
            $value,
            $type
        );
    }

    public function execute(): void
    {
        $this->stmt->execute();
    }

    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    public function fetch(): mixed
    {
        $this->execute();
        return $this->stmt
            ->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll(): array
    {
        $this->execute();
        return $this->stmt
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
