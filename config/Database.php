<?php

class Database {
    private $conn = null;

    public function getConnection(): PDO {
        if ($this->conn !== null) {
            return $this->conn;
        }

        $databaseUrl = getenv('DATABASE_URL');

        if ($databaseUrl) {
            $params = parse_url($databaseUrl);
            $host   = $params['host'];
            $port   = $params['port'] ?? 5432;
            $db     = ltrim($params['path'], '/');
            $user   = $params['user'];
            $pass   = $params['pass'];
            $dsn    = "pgsql:host={$host};port={$port};dbname={$db};sslmode=require";
        } else {
            $host = getenv('DB_HOST')     ?: 'localhost';
            $port = getenv('DB_PORT')     ?: '5432';
            $db   = getenv('DB_NAME')     ?: 'quotesdb';
            $user = getenv('DB_USER')     ?: 'postgres';
            $pass = getenv('DB_PASSWORD') ?: '';
            $dsn  = "pgsql:host={$host};port={$port};dbname={$db}";
        }

        try {
            $this->conn = new PDO($dsn, $user ?? null, $pass ?? null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Database connection failed: ' . $e->getMessage()]);
            exit;
        }

        return $this->conn;
    }
}