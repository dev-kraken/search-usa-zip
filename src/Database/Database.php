<?php

declare(strict_types=1);

namespace DevKraken\Database;

use PDO;
use PDOException;
use RuntimeException;

class Database implements DatabaseInterface
{
    private ?PDO $conn = null;

    public function getConnection(): PDO
    {
        if ($this->conn === null) {
            try {
                $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4';
                $this->conn = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                throw new RuntimeException("Connection failed: ".$e->getMessage());
            }
        }
        return $this->conn;
    }

    public function closeConnection(): void
    {
        $this->conn = null;
    }
}