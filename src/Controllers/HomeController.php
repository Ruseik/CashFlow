<?php
declare(strict_types=1);

namespace Controllers;

use Models\Transaction;

class HomeController extends Controller {
    private Transaction $transactionModel;

    public function __construct() {
        $this->transactionModel = new Transaction();
    }

    public function index(): void {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $recentTransactions = $this->transactionModel->getRecentTransactions($userId, 5);
        $this->render('home/index', [
            'recentTransactions' => $recentTransactions,
            'username' => $_SESSION['username'] ?? 'User',
        ]);
    }
}
