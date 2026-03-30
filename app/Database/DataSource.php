<?php

namespace App\Database;

use App\Database\Connection;
use PDO;

class DataSource
{
    private static ?DataSource $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    public static function getInstance(): DataSource
    {
        if (self::$instance === null) {
            self::$instance = new DataSource();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function select(string $sql, array $params = []): array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Executa uma instrução de inserção.
     * @return int O número de linhas afetadas.
     */
    public function insert(string $query, array $params): int
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    /**
     * Executa uma instrução de inserção e retorna o último ID inserido.
     * @return string O ID do último registro inserido.
     */
    public function insertWithLastId(string $query, array $params): string
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $this->connection->lastInsertId();
    }

    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollback(): bool
    {
        return $this->connection->rollBack();
    }
}
