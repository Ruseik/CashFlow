<?php
declare(strict_types=1);

namespace Models;

class Purpose extends Model {
    protected string $table = 'purposes';
    protected array $fillable = [
        'name',
        'description',
        'user_id',
        'show_in_basic_mode'
    ];
    
    public function findByUser(int $userId, bool $basicModeOnly = false): array {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        
        if ($basicModeOnly) {
            $sql .= " AND show_in_basic_mode = 1";
        }
        
        $sql .= " ORDER BY name ASC";
        
        $stmt = $this->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getTransactionStats(int $purposeId, ?string $startDate = null, ?string $endDate = null): array {
        $sql = "SELECT 
                    c.code as currency,
                    SUM(t.start_amount) as total_amount,
                    COUNT(*) as transaction_count
                FROM transactions t
                JOIN currencies c ON t.start_currency_id = c.id
                WHERE t.purpose_id = ?";
        
        $params = [$purposeId];
        
        if ($startDate) {
            $sql .= " AND t.date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND t.date <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " GROUP BY c.code";
        
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
