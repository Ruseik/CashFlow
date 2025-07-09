<?php
declare(strict_types=1);

namespace Controllers;

use Models\Transaction;
use Models\Entity;
use Models\Currency;

class AnalyticsController extends Controller {
    private Transaction $transactionModel;

    public function __construct() {
        $this->transactionModel = new Transaction();
    }

    public function index(): void {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-01', strtotime('-11 months', strtotime($endDate)));
        $analytics = $this->transactionModel->getAnalytics($userId, $startDate, $endDate);

        // --- BALANCE SECTION LOGIC ---
        $entityModel = new Entity();
        $currencyModel = new Currency();
        $entities = $entityModel->findByUser($userId);
        $lkrCurrency = $currencyModel->findByCode('LKR');
        $lkrCurrencyId = $lkrCurrency ? $lkrCurrency['id'] : null;
        $balances = [];
        if ($lkrCurrencyId) {
            foreach ($entities as $entity) {
                $balanceArr = $entityModel->getBalance($entity['id'], $lkrCurrencyId);
                $balance = 0;
                foreach ($balanceArr as $row) {
                    if ($row['currency'] === 'LKR') {
                        $balance = (float)$row['balance'];
                        break;
                    }
                }
                $balances[] = [
                    'id' => $entity['id'],
                    'name' => $entity['name'],
                    'balance' => $balance
                ];
            }
        }
        // Custom order: Pocket, HSBC Credit Card, Peoples Bank Account, then others
        $priority = ['Pocket', 'HSBC Credit Card', 'Peoples Bank Account'];
        usort($balances, function($a, $b) use ($priority) {
            $aIndex = array_search($a['name'], $priority);
            $bIndex = array_search($b['name'], $priority);
            if ($aIndex === false) $aIndex = 100;
            if ($bIndex === false) $bIndex = 100;
            if ($aIndex === $bIndex) return strcmp($a['name'], $b['name']);
            return $aIndex - $bIndex;
        });
        // --- END BALANCE SECTION LOGIC ---

        $this->render('analytics/index', [
            'analytics' => $analytics,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'balances' => $balances
        ]);
    }
}
