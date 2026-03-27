<?php

class Quote {
    private PDO $conn;
    private string $table = 'quotes';

    public int    $id;
    public string $quote;
    public int    $author_id;
    public int    $category_id;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    private function baseSelect(): string {
        return "SELECT q.id, q.quote, a.author, c.category
                FROM {$this->table} q
                JOIN authors    a ON q.author_id   = a.id
                JOIN categories c ON q.category_id = c.id";
    }

    public function getAll(bool $random = false): PDOStatement {
        $sql = $this->baseSelect();
        if ($random) $sql .= " ORDER BY RANDOM() LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function getById(): PDOStatement {
        $sql  = $this->baseSelect() . " WHERE q.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function getByAuthor(bool $random = false): PDOStatement {
        $sql = $this->baseSelect() . " WHERE q.author_id = :author_id";
        if ($random) $sql .= " ORDER BY RANDOM() LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':author_id', $this->author_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function getByCategory(bool $random = false): PDOStatement {
        $sql = $this->baseSelect() . " WHERE q.category_id = :category_id";
        if ($random) $sql .= " ORDER BY RANDOM() LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function getByAuthorAndCategory(bool $random = false): PDOStatement {
        $sql = $this->baseSelect() . " WHERE q.author_id = :author_id AND q.category_id = :category_id";
        if ($random) $sql .= " ORDER BY RANDOM() LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':author_id',   $this->author_id,   PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function authorExists(int $authorId): bool {
        $stmt = $this->conn->prepare("SELECT id FROM authors WHERE id = :id");
        $stmt->bindParam(':id', $authorId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function categoryExists(int $categoryId): bool {
        $stmt = $this->conn->prepare("SELECT id FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create(): bool {
        $sql  = "INSERT INTO {$this->table} (quote, author_id, category_id)
                 VALUES (:quote, :author_id, :category_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':quote',       $this->quote);
        $stmt->bindParam(':author_id',   $this->author_id,   PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);

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

        $sql  = "UPDATE {$this->table}
                 SET quote = :quote, author_id = :author_id, category_id = :category_id
                 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':quote',       $this->quote);
        $stmt->bindParam(':author_id',   $this->author_id,   PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindParam(':id',          $this->id,          PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(): bool|string {
        $check = $this->conn->prepare("SELECT id FROM {$this->table} WHERE id = :id");
        $check->bindParam(':id', $this->id, PDO::PARAM_INT);
        $check->execute();
        if ($check->rowCount() === 0) return 'not_found';

        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}