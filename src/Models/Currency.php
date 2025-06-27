<?php
declare(strict_types=1);

namespace Models;

class Currency extends Model {
    protected string $table = 'currencies';
    protected array $fillable = [
        'code',
        'name',
        'symbol',
        'show_in_basic_mode'
    ];
    
    public function findByCode(string $code): ?array {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch() ?: null;
    }
    
    public function getAllActive(bool $basicModeOnly = false): array {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($basicModeOnly) {
            $sql .= " WHERE show_in_basic_mode = 1";
        }
        
        $sql .= " ORDER BY code ASC";
        
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getExchangeRate(int $fromCurrencyId, int $toCurrencyId, string $date): float {
        // This is a placeholder. In a real application, you would:
        // 1. Check your own exchange rates table first
        // 2. If not found, call an external API
        // 3. Store the result in your database
        // 4. Return the exchange rate
        
        // For now, we'll return 1 if same currency, otherwise null
        return $fromCurrencyId === $toCurrencyId ? 1.0 : 0.0;
    }
    
    public function recordExchangeRate(int $fromCurrencyId, int $toCurrencyId, string $date, float $rate): bool {
        // This would store the exchange rate in a rates table
        // Implementation depends on your exchange rate tracking needs
        return true;
    }
}
