<?php

namespace App\Database;

// Importamos explicitamente do escopo global
use \PDO;
use \PDOException;

class Connection {
    private static ?PDO $instance = null;

    public static function getInstance(): \PDO {
        if (self::$instance === null) {
            // Captura as variáveis de ambiente
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $db   = $_ENV['DB_DATABASE'] ?? $_ENV['DB_NAME'] ?? '';
            $user = $_ENV['DB_USERNAME'] ?? $_ENV['DB_USER'] ?? '';
            $pass = $_ENV['DB_PASSWORD'] ?? $_ENV['DB_PASS'] ?? '';
            $port = $_ENV['DB_PORT'] ?? '3306';

            try {
                // Usamos \PDO para garantir o acesso à classe nativa
                self::$instance = new \PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
                self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
                
                // Força o fuso horário
                self::$instance->exec("SET time_zone = '-03:00'");

            } catch (\PDOException $e) {
                // Em produção, evite o die() direto, mas para debug é útil:
                header('Content-Type: text/html; charset=utf-8');
                die("Erro crítico de conexão: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}