<?php

class Author {
    private PDO $conn;
    private string $table = 'authors';

    public int    $id;
    public string $author;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function getAll(): PDOStatement {
        $stmt = $this->conn->prepare("SELECT id, author FROM {$this->table} ORDER BY id");
        $stmt->execute();
        return $stmt;
    }

    public function getById(): PDOStatement {
        $stmt = $this->conn->prepare("SELECT id, author FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function create(): bool {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (author) VALUES (:author)");
        $stmt->bindParam(':author', $this->author);
        if ($stmt->execute()) {
            $this->id = (int) $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update(): bool|string {
        $check = $this->conn->prepare("SELECT id FROM {$this->table} WHERE id = :id");
        $check->bindParam(':id', $this->id, PDO::PARAM_INT);
        $check->execute();
        if ($check->rowCount() === 0) return 'not_found';

        $stmt = $this->conn->prepare("UPDATE {$this->table} SET author = :author WHERE id = :id");
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':id',     $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(): bool|string {
        $check = $this->conn->prepare("SELECT id FROM {$this->table} WHERE id = :id");
        $check->bindParam(':id', $this->id, PDO::PARAM_INT);
        $check->execute();
        if ($check->rowCount() === 0) return 'not_found';

        try {
            $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return 'fk_error';
        }
    }
}