<?php
declare(strict_types=1);

namespace Controllers;

use Models\Transaction;

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
        $this->render('analytics/index', [
            'analytics' => $analytics,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}
