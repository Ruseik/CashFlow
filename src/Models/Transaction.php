<?php
declare(strict_types=1);

namespace Models;

class Transaction extends Model {
    protected string $table = 'transactions';
    protected array $fillable = [
        'name',
        'user_id',
        'start_entity_id',
        'dest_entity_id',
        'start_amount',
        'start_currency_id',
        'dest_amount',
        'dest_currency_id',
        'fee_entity_id',
        'fee_amount',
        'fee_currency_id',
        'date',
        'purpose_id',
        'mode_id',
        'remarks',
        'grid_profit'
    ];

    public function getRecentTransactions(int $userId, int $limit = 10): array {
        $sql = "SELECT 
                    t.*,
                    se.name as start_entity_name,
                    de.name as dest_entity_name,
                    fe.name as fee_entity_name,
                    sc.code as start_currency_code,
                    dc.code as dest_currency_code,
                    fc.code as fee_currency_code,
                    p.name as purpose_name,
                    m.name as mode_name
                FROM {$this->table} t
                JOIN entities se ON t.start_entity_id = se.id
                JOIN entities de ON t.dest_entity_id = de.id
                LEFT JOIN entities fe ON t.fee_entity_id = fe.id
                JOIN currencies sc ON t.start_currency_id = sc.id
                JOIN currencies dc ON t.dest_currency_id = dc.id
                LEFT JOIN currencies fc ON t.fee_currency_id = fc.id
                JOIN purposes p ON t.purpose_id = p.id
                JOIN modes m ON t.mode_id = m.id
                WHERE t.user_id = ?
                ORDER BY t.date DESC, t.created_at DESC
                LIMIT ?";

        $stmt = $this->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function validateTransaction(array $data, bool $isBasicMode = false): array {
        $errors = [];
        
        // Required fields for both modes
        $requiredFields = [
            'name' => 'Transaction name is required',
            'start_entity_id' => 'Source entity is required',
            'start_amount' => 'Amount is required',
            'date' => 'Date is required',
            'purpose_id' => 'Purpose is required',
            'mode_id' => 'Mode is required'
        ];

        // Additional required fields for full mode
        if (!$isBasicMode) {
            $requiredFields = array_merge($requiredFields, [
                'dest_entity_id' => 'Destination entity is required',
                'dest_amount' => 'Destination amount is required',
                'fee_entity_id' => 'Fee entity is required',
                'fee_amount' => 'Fee amount is required'
            ]);
        }

        // Check required fields
        foreach ($requiredFields as $field => $message) {
            // For amount fields, allow zero (0 or '0')
            if (in_array($field, ['start_amount', 'dest_amount', 'fee_amount'])) {
                if (!isset($data[$field]) || $data[$field] === '' || !is_numeric($data[$field])) {
                    $errors[$field] = $message;
                }
            } else {
                if (!isset($data[$field]) || $data[$field] === '') {
                    $errors[$field] = $message;
                }
            }
        }

        // Validate amounts
        if (isset($data['start_amount']) && $data['start_amount'] !== '' && !is_numeric($data['start_amount'])) {
            $errors['start_amount'] = 'Amount must be a number';
        }

        if (!$isBasicMode) {
            if (isset($data['dest_amount']) && $data['dest_amount'] !== '' && !is_numeric($data['dest_amount'])) {
                $errors['dest_amount'] = 'Destination amount must be a number';
            }
            if (isset($data['fee_amount']) && $data['fee_amount'] !== '' && !is_numeric($data['fee_amount'])) {
                $errors['fee_amount'] = 'Fee amount must be a number';
            }
        }

        // Validate date
        if (!empty($data['date'])) {
            $date = date_create($data['date']);
            if (!$date) {
                $errors['date'] = 'Invalid date format';
            }
        }

        return $errors;
    }

    public function createTransaction(array $data, bool $isBasicMode = false): int {
        // Remove the merge for basic mode here, as it is already handled in the controller
        error_log('DEBUG: createTransaction received data: ' . var_export($data, true));
        // Start transaction
        self::$db->beginTransaction();
        try {
            $id = $this->create($data);

            // Additional business logic can be added here
            // For example, updating balances, recording exchange rates, etc.

            self::$db->commit();
            return $id;
        } catch (\Exception $e) {
            self::$db->rollBack();
            throw $e;
        }
    }

    public function getAnalytics(int $userId, string $startDate, string $endDate): array {
        $sql = "SELECT 
                    DATE_FORMAT(t.date, '%Y-%m') as month,
                    c.code as currency,
                    SUM(CASE 
                        WHEN t.start_entity_id != 1 AND t.dest_entity_id != 1 THEN 0
                        ELSE t.start_amount 
                    END) as total_amount,
                    SUM(t.fee_amount) as total_fees,
                    SUM(t.grid_profit) as total_profit,
                    COUNT(*) as transaction_count
                FROM transactions t
                JOIN currencies c ON t.start_currency_id = c.id
                WHERE t.user_id = ?
                    AND t.date BETWEEN ? AND ?
                GROUP BY DATE_FORMAT(t.date, '%Y-%m'), c.code
                ORDER BY month DESC, c.code";

        $stmt = $this->prepare($sql);
        $stmt->execute([$userId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }

    /**
     * Get transactions filtered by date range and optional entity/currency filters.
     */
    public function getFilteredTransactions(int $userId, string $startDate, string $endDate, array $filters = []): array {
        $sql = "SELECT 
                    t.*,
                    se.name as start_entity_name,
                    de.name as dest_entity_name,
                    fe.name as fee_entity_name,
                    sc.code as start_currency_code,
                    dc.code as dest_currency_code,
                    fc.code as fee_currency_code,
                    p.name as purpose_name,
                    m.name as mode_name
                FROM {$this->table} t
                JOIN entities se ON t.start_entity_id = se.id
                JOIN entities de ON t.dest_entity_id = de.id
                LEFT JOIN entities fe ON t.fee_entity_id = fe.id
                JOIN currencies sc ON t.start_currency_id = sc.id
                JOIN currencies dc ON t.dest_currency_id = dc.id
                LEFT JOIN currencies fc ON t.fee_currency_id = fc.id
                JOIN purposes p ON t.purpose_id = p.id
                JOIN modes m ON t.mode_id = m.id
                WHERE t.user_id = ?
                  AND t.date BETWEEN ? AND ?";
        $params = [$userId, $startDate, $endDate];

        // Optional filters
        if (!empty($filters['from_entities'])) {
            $in = implode(',', array_fill(0, count($filters['from_entities']), '?'));
            $sql .= " AND t.start_entity_id IN ($in)";
            $params = array_merge($params, $filters['from_entities']);
        }
        if (!empty($filters['to_entities'])) {
            $in = implode(',', array_fill(0, count($filters['to_entities']), '?'));
            $sql .= " AND t.dest_entity_id IN ($in)";
            $params = array_merge($params, $filters['to_entities']);
        }
        if (!empty($filters['currencies'])) {
            $in = implode(',', array_fill(0, count($filters['currencies']), '?'));
            $sql .= " AND (t.start_currency_id IN ($in) OR t.dest_currency_id IN ($in))";
            $params = array_merge($params, $filters['currencies'], $filters['currencies']);
        }

        $sql .= " ORDER BY t.date DESC, t.created_at DESC";

        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Get expenditure analytics for a user (where dest_entity_id is 'void' entity).
     * Returns: expenditures list, total, avg, count, monthly breakdown.
     */
    public function getExpendituresAnalytics(int $userId, int $voidEntityId, string $startDate, string $endDate, ?int $currencyId = null): array {
        // Main expenditures query
        $params = [$userId, $voidEntityId, $startDate, $endDate];
        $currencySql = '';
        if ($currencyId) {
            $currencySql = ' AND t.start_currency_id = ?';
            $params[] = $currencyId;
        }
        $sql = "SELECT t.date, t.start_amount as amount, c.code as currency, t.remarks as description
                FROM {$this->table} t
                JOIN currencies c ON t.start_currency_id = c.id
                WHERE t.user_id = ?
                  AND t.dest_entity_id = ?
                  AND t.date BETWEEN ? AND ?
                  $currencySql
                ORDER BY t.date DESC, t.created_at DESC";
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        $expenditures = $stmt->fetchAll();

        // Summary stats
        $total = 0;
        $count = count($expenditures);
        $monthly = [];
        foreach ($expenditures as $exp) {
            $total += (float)$exp['amount'];
            $month = date('Y-m', strtotime($exp['date']));
            if (!isset($monthly[$month])) $monthly[$month] = 0;
            $monthly[$month] += (float)$exp['amount'];
        }
        $avg = $count > 0 ? $total / $count : 0;
        // Format for chart
        $monthlyArr = [];
        foreach ($monthly as $month => $amount) {
            $monthlyArr[] = ['month' => $month, 'amount' => $amount];
        }
        // Sort months ascending
        usort($monthlyArr, fn($a, $b) => strcmp($a['month'], $b['month']));

        return [
            'expenditures' => $expenditures,
            'total' => $total,
            'avg' => $avg,
            'count' => $count,
            'by_month' => $monthlyArr
        ];
    }

    /**
     * Get advanced expenditure breakdowns: by entity, by purpose, by mode.
     */
    public function getExpenditureBreakdowns(int $userId, int $voidEntityId, string $startDate, string $endDate, ?int $currencyId = null): array {
        $params = [$userId, $voidEntityId, $startDate, $endDate];
        $currencySql = '';
        if ($currencyId) {
            $currencySql = ' AND t.start_currency_id = ?';
            $params[] = $currencyId;
        }
        // By entity (start_entity)
        $sqlEntity = "SELECT se.name as entity, SUM(t.start_amount) as amount
                      FROM {$this->table} t
                      JOIN entities se ON t.start_entity_id = se.id
                      WHERE t.user_id = ? AND t.dest_entity_id = ? AND t.date BETWEEN ? AND ? $currencySql
                      GROUP BY se.name ORDER BY amount DESC";
        $stmt = $this->prepare($sqlEntity);
        $stmt->execute($params);
        $by_entity = $stmt->fetchAll();
        // By purpose
        $sqlPurpose = "SELECT p.name as purpose, SUM(t.start_amount) as amount
                       FROM {$this->table} t
                       JOIN purposes p ON t.purpose_id = p.id
                       WHERE t.user_id = ? AND t.dest_entity_id = ? AND t.date BETWEEN ? AND ? $currencySql
                       GROUP BY p.name ORDER BY amount DESC";
        $stmt = $this->prepare($sqlPurpose);
        $stmt->execute($params);
        $by_purpose = $stmt->fetchAll();
        // By mode
        $sqlMode = "SELECT m.name as mode, SUM(t.start_amount) as amount
                    FROM {$this->table} t
                    JOIN modes m ON t.mode_id = m.id
                    WHERE t.user_id = ? AND t.dest_entity_id = ? AND t.date BETWEEN ? AND ? $currencySql
                    GROUP BY m.name ORDER BY amount DESC";
        $stmt = $this->prepare($sqlMode);
        $stmt->execute($params);
        $by_mode = $stmt->fetchAll();
        return [
            'by_entity' => $by_entity,
            'by_purpose' => $by_purpose,
            'by_mode' => $by_mode
        ];
    }
}
