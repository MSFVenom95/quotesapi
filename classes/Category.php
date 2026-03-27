<?php

class Category {
    private PDO $conn;
    private string $table = 'categories';

    public int    $id;
    public string $category;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function getAll(): PDOStatement {
        $stmt = $this->conn->prepare("SELECT id, category FROM {$this->table} ORDER BY id");
        $stmt->execute();
        return $stmt;
    }

    public function getById(): PDOStatement {
        $stmt = $this->conn->prepare("SELECT id, category FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function create(): bool {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (category) VALUES (:category)");
        $stmt->bindParam(':category', $this->category);
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

        $stmt = $this->conn->prepare("UPDATE {$this->table} SET category = :category WHERE id = :id");
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':id',       $this->id, PDO::PARAM_INT);
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