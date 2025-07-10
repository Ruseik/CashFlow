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
        $endDate = $_GET['date_to'] ?? date('Y-m-d');
        $startDate = $_GET['date_from'] ?? date('Y-m-01', strtotime('-11 months', strtotime($endDate)));
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

        // --- EXPENDITURE ANALYTICS LOGIC ---
        $voidEntity = $entityModel->findByName('void');
        $voidEntityId = $voidEntity ? $voidEntity['id'] : null;
        $exp_currency = $_GET['currency'] ?? null;
        $exp_currency_id = null;
        $currencies = $currencyModel->getAllActive();
        if ($exp_currency) {
            foreach ($currencies as $cur) {
                if ($cur['code'] === $exp_currency) {
                    $exp_currency_id = $cur['id'];
                    break;
                }
            }
        }
        $exp_date_from = $_GET['date_from'] ?? null;
        $exp_date_to = $_GET['date_to'] ?? null;
        $exp_data = [
            'expenditures' => [], 'total' => 0, 'avg' => 0, 'count' => 0, 'by_month' => []
        ];
        $exp_breakdowns = [
            'by_entity' => [], 'by_purpose' => [], 'by_mode' => []
        ];
        if ($voidEntityId) {
            $exp_data = $this->transactionModel->getExpendituresAnalytics(
                $userId,
                $voidEntityId,
                $exp_date_from ?? $startDate,
                $exp_date_to ?? $endDate,
                $exp_currency_id
            );
            $exp_breakdowns = $this->transactionModel->getExpenditureBreakdowns(
                $userId,
                $voidEntityId,
                $exp_date_from ?? $startDate,
                $exp_date_to ?? $endDate,
                $exp_currency_id
            );
        }
        // --- END EXPENDITURE ANALYTICS LOGIC ---

        $this->render('analytics/index', [
            'analytics' => $analytics,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'balances' => $balances,
            // Expenditure analytics
            'expenditures' => $exp_data['expenditures'],
            'exp_total' => $exp_data['total'],
            'exp_avg' => $exp_data['avg'],
            'exp_count' => $exp_data['count'],
            'exp_by_month' => $exp_data['by_month'],
            'exp_date_from' => $exp_date_from,
            'exp_date_to' => $exp_date_to,
            'exp_currency' => $exp_currency,
            'currencies' => $currencies,
            // Advanced breakdowns
            'exp_by_entity' => $exp_breakdowns['by_entity'],
            'exp_by_purpose' => $exp_breakdowns['by_purpose'],
            'exp_by_mode' => $exp_breakdowns['by_mode']
        ]);
    }
}
