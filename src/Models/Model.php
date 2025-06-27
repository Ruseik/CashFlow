<?php
declare(strict_types=1);

namespace Models;

class Model {
    protected static \PDO $db;
    protected string $table;
    protected array $fillable = [];
    protected string $primaryKey = 'id';
    
    public static function setDB(\PDO $pdo): void {
        self::$db = $pdo;
    }
    
    protected function prepare(string $sql): \PDOStatement {
        return self::$db->prepare($sql);
    }
    
    public function find(int $id): ?array {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    
    public function findAll(array $conditions = [], array $orderBy = []): array {
        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', array_map(fn($key) => "$key = ?", array_keys($conditions)));
        }
        
        if (!empty($orderBy)) {
            $sql .= " ORDER BY " . implode(', ', array_map(
                fn($key, $dir) => "$key $dir",
                array_keys($orderBy),
                $orderBy
            ));
        }
        
        $stmt = $this->prepare($sql);
        $stmt->execute(array_values($conditions));
        return $stmt->fetchAll();
    }
    
    public function create(array $data): int {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', array_keys($fields)),
            implode(', ', array_fill(0, count($fields), '?'))
        );
        
        $stmt = $this->prepare($sql);
        $stmt->execute(array_values($fields));
        
        return (int)self::$db->lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s = ?",
            $this->table,
            implode(', ', array_map(fn($field) => "$field = ?", array_keys($fields))),
            $this->primaryKey
        );
        
        $stmt = $this->prepare($sql);
        return $stmt->execute([...array_values($fields), $id]);
    }
    
    public function delete(int $id): bool {
        $stmt = $this->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    public function getDb() {
        return self::$db;
    }
}
