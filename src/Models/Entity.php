<?php
declare(strict_types=1);

namespace Models;

class Entity extends Model {
    protected string $table = 'entities';
    protected array $fillable = [
        'name',
        'description',
        'user_id',
        'show_in_basic_mode'
    ];
    
    public function findByUser(int $userId, bool $basicModeOnly = false): array {
        $sql = "SELECT * FROM {$this->table} WHERE (user_id = ? OR user_id = 1)";  // Include system entities
        
        if ($basicModeOnly) {
            $sql .= " AND show_in_basic_mode = 1";
        }
        
        $sql .= " ORDER BY name ASC";
        
        $stmt = $this->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getTransactions(int $entityId): array {
        $sql = "SELECT t.* FROM transactions t 
                WHERE t.start_entity_id = ? 
                OR t.dest_entity_id = ? 
                OR t.fee_entity_id = ?
                ORDER BY t.date DESC, t.created_at DESC";
        
        $stmt = $this->prepare($sql);
        $stmt->execute([$entityId, $entityId, $entityId]);
        return $stmt->fetchAll();
    }
    
    public function getBalance(int $entityId, ?int $currencyId = null): array {
        $sql = "SELECT 
                    COALESCE(c.code, 'TOTAL') as currency,
                    SUM(
                        CASE 
                            WHEN t.start_entity_id = ? THEN -t.start_amount
                            WHEN t.dest_entity_id = ? THEN t.dest_amount
                            WHEN t.fee_entity_id = ? THEN t.fee_amount
                            ELSE 0 
                        END
                    ) as balance
                FROM transactions t
                LEFT JOIN currencies c ON 
                    CASE 
                        WHEN t.start_entity_id = ? THEN t.start_currency_id
                        WHEN t.dest_entity_id = ? THEN t.dest_currency_id
                        WHEN t.fee_entity_id = ? THEN t.fee_currency_id
                    END = c.id";
        
        if ($currencyId) {
            $sql .= " WHERE 
                        (t.start_entity_id = ? AND t.start_currency_id = ?) OR
                        (t.dest_entity_id = ? AND t.dest_currency_id = ?) OR
                        (t.fee_entity_id = ? AND t.fee_currency_id = ?)";
        }
        
        $sql .= " GROUP BY c.code WITH ROLLUP";
        
        $stmt = $this->prepare($sql);
        
        if ($currencyId) {
            $stmt->execute([
                $entityId, $entityId, $entityId, 
                $entityId, $entityId, $entityId,
                $entityId, $currencyId,
                $entityId, $currencyId,
                $entityId, $currencyId
            ]);
        } else {
            $stmt->execute([
                $entityId, $entityId, $entityId,
                $entityId, $entityId, $entityId
            ]);
        }
        
        return $stmt->fetchAll();
    }
    
    public function findByName(string $name): ?array {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        return $stmt->fetch() ?: null;
    }
}
