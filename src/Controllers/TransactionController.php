<?php
declare(strict_types=1);

namespace Controllers;

use Models\Transaction;
use Models\Entity;
use Models\Currency;
use Models\Purpose;
use Models\Mode;

class TransactionController extends Controller {
    private Transaction $transactionModel;
    private Entity $entityModel;
    private Currency $currencyModel;
    private Purpose $purposeModel;
    private Mode $modeModel;

    public function __construct() {
        $this->transactionModel = new Transaction();
        $this->entityModel = new Entity();
        $this->currencyModel = new Currency();
        $this->purposeModel = new Purpose();
        $this->modeModel = new Mode();
    }

    public function index(): void {
        $this->requireAuth();

        $userId = $_SESSION['user_id'];
        $transactions = $this->transactionModel->getRecentTransactions($userId, 20);

        $this->render('transactions/index', [
            'transactions' => $transactions
        ]);
    }

    public function create(): void {
        $this->requireAuth();

        $userId = $_SESSION['user_id'];
        $isBasicMode = (($_GET['mode'] ?? 'basic') === 'basic'); // <-- FIXED

        // Get all necessary data for dropdowns
        $entities = $this->entityModel->findByUser($userId, $isBasicMode);
        $currencies = $this->currencyModel->getAllActive($isBasicMode);
        $purposes = $this->purposeModel->findByUser($userId, $isBasicMode);
        $modes = $this->modeModel->findByUser($userId, $isBasicMode);

        $this->render('transactions/create', [
            'entities' => $entities,
            'currencies' => $currencies,
            'purposes' => $purposes,
            'modes' => $modes,
            'isBasicMode' => $isBasicMode,
            'csrf_token' => $this->csrf()
        ]);
    }

    public function store(): void {
        $this->requireAuth();
        $this->validateCsrf();

        $userId = $_SESSION['user_id'];
        $isBasicMode = $_POST['mode'] === 'basic';

        // Prepare transaction data
        $data = [
            'name' => trim($_POST['name']),
            'user_id' => $userId,
            'start_entity_id' => (int)$_POST['start_entity_id'],
            'start_amount' => (float)$_POST['start_amount'],
            'start_currency_id' => (int)$_POST['start_currency_id'],
            'date' => $_POST['date'],
            'purpose_id' => (int)$_POST['purpose_id'],
            'mode_id' => (int)$_POST['mode_id'],
            'remarks' => trim($_POST['remarks'] ?? '')
        ];

        if ($isBasicMode) {
            // Robust: fetch Void entity id dynamically
            $voidEntity = $this->entityModel->findByName('Void');
            $voidEntityId = $voidEntity ? (int)$voidEntity['id'] : null;
            error_log('DEBUG: Void entity id used for dest_entity_id: ' . var_export($voidEntityId, true));
            error_log('DEBUG: Transaction data: ' . var_export($data, true));
            if (!$voidEntityId) {
                $_SESSION['error'] = 'System error: Void entity not found.';
                $this->redirect('/transactions/create?mode=basic');
                return;
            }
            $data = array_merge($data, [
                'dest_entity_id' => $voidEntityId,
                'dest_amount' => 0,
                'dest_currency_id' => $data['start_currency_id'],
                'fee_entity_id' => $data['start_entity_id'],
                'fee_amount' => 0,
                'fee_currency_id' => $data['start_currency_id'],
                'grid_profit' => 0
            ]);
            error_log('DEBUG: Transaction data after merge: ' . var_export($data, true));
        } else {
            $data = array_merge($data, [
                'dest_entity_id' => (int)$_POST['dest_entity_id'],
                'dest_amount' => (float)$_POST['dest_amount'],
                'dest_currency_id' => (int)$_POST['dest_currency_id'],
                'fee_entity_id' => (int)$_POST['fee_entity_id'],
                'fee_amount' => (float)$_POST['fee_amount'],
                'fee_currency_id' => (int)$_POST['fee_currency_id'],
                'grid_profit' => (float)($_POST['grid_profit'] ?? 0)
            ]);
        }

        // Validate
        $errors = $this->transactionModel->validateTransaction($data, $isBasicMode);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/transactions/create' . ($isBasicMode ? '?mode=basic' : '?mode=full'));
            return;
        }

        try {
            $id = $this->transactionModel->createTransaction($data, $isBasicMode);
            $_SESSION['success'] = 'Transaction created successfully';
            $this->redirect('/transactions/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to create transaction: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            $this->redirect('/transactions/create' . ($isBasicMode ? '?mode=basic' : '?mode=full'));
        }
    }

    public function show(int $id): void {
        $this->requireAuth();

        $transaction = $this->transactionModel->find($id);

        if (!$transaction || $transaction['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/transactions');
            return;
        }

        // Get related data
        $startEntity = $this->entityModel->find($transaction['start_entity_id']);
        $destEntity = $this->entityModel->find($transaction['dest_entity_id']);
        $feeEntity = $this->entityModel->find($transaction['fee_entity_id']);
        
        $startCurrency = $this->currencyModel->find($transaction['start_currency_id']);
        $destCurrency = $this->currencyModel->find($transaction['dest_currency_id']);
        $feeCurrency = $this->currencyModel->find($transaction['fee_currency_id']);
        
        $purpose = $this->purposeModel->find($transaction['purpose_id']);
        $mode = $this->modeModel->find($transaction['mode_id']);

        $this->render('transactions/show', [
            'transaction' => $transaction,
            'startEntity' => $startEntity,
            'destEntity' => $destEntity,
            'feeEntity' => $feeEntity,
            'startCurrency' => $startCurrency,
            'destCurrency' => $destCurrency,
            'feeCurrency' => $feeCurrency,
            'purpose' => $purpose,
            'mode' => $mode
        ]);
    }

    public function edit(int $id): void {
        $this->requireAuth();

        $transaction = $this->transactionModel->find($id);

        if (!$transaction || $transaction['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/transactions');
            return;
        }

        $userId = $_SESSION['user_id'];
        $isBasicMode = $transaction['dest_entity_id'] === 1 && 
                      $transaction['dest_amount'] === 0 && 
                      $transaction['fee_amount'] === 0;

        // Get all necessary data for dropdowns
        $entities = $this->entityModel->findByUser($userId, $isBasicMode);
        $currencies = $this->currencyModel->getAllActive($isBasicMode);
        $purposes = $this->purposeModel->findByUser($userId, $isBasicMode);
        $modes = $this->modeModel->findByUser($userId, $isBasicMode);

        $this->render('transactions/edit', [
            'transaction' => $transaction,
            'entities' => $entities,
            'currencies' => $currencies,
            'purposes' => $purposes,
            'modes' => $modes,
            'isBasicMode' => $isBasicMode,
            'csrf_token' => $this->csrf()
        ]);
    }

    public function update(int $id): void {
        $this->requireAuth();
        $this->validateCsrf();

        $transaction = $this->transactionModel->find($id);

        if (!$transaction || $transaction['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/transactions');
            return;
        }

        $isBasicMode = $_POST['mode'] === 'basic';
        $userId = $_SESSION['user_id'];

        // Prepare transaction data
        $data = [
            'name' => trim($_POST['name']),
            'user_id' => $userId,
            'start_entity_id' => (int)$_POST['start_entity_id'],
            'start_amount' => (float)$_POST['start_amount'],
            'start_currency_id' => (int)$_POST['start_currency_id'],
            'date' => $_POST['date'],
            'purpose_id' => (int)$_POST['purpose_id'],
            'mode_id' => (int)$_POST['mode_id'],
            'remarks' => trim($_POST['remarks'] ?? '')
        ];

        if ($isBasicMode) {
            $voidEntity = $this->entityModel->findByName('Void');
            $voidEntityId = $voidEntity ? (int)$voidEntity['id'] : null;
            if (!$voidEntityId) {
                $_SESSION['error'] = 'System error: Void entity not found.';
                $this->redirect('/transactions/' . $id . '/edit');
                return;
            }
            $data = array_merge($data, [
                'dest_entity_id' => $voidEntityId,
                'dest_amount' => 0,
                'dest_currency_id' => $data['start_currency_id'],
                'fee_entity_id' => $data['start_entity_id'],
                'fee_amount' => 0,
                'fee_currency_id' => $data['start_currency_id'],
                'grid_profit' => 0
            ]);
        } else {
            $data = array_merge($data, [
                'dest_entity_id' => (int)$_POST['dest_entity_id'],
                'dest_amount' => (float)$_POST['dest_amount'],
                'dest_currency_id' => (int)$_POST['dest_currency_id'],
                'fee_entity_id' => (int)$_POST['fee_entity_id'],
                'fee_amount' => (float)$_POST['fee_amount'],
                'fee_currency_id' => (int)$_POST['fee_currency_id'],
                'grid_profit' => (float)($_POST['grid_profit'] ?? 0)
            ]);
        }

        // Validate
        $errors = $this->transactionModel->validateTransaction($data, $isBasicMode);
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/transactions/' . $id . '/edit');
            return;
        }

        // Update transaction in DB transaction
        $db = $this->transactionModel->getDb();
        $db->beginTransaction();
        try {
            $this->transactionModel->update($id, $data);
            $db->commit();
            $_SESSION['success'] = 'Transaction updated successfully';
            $this->redirect('/transactions/' . $id);
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Failed to update transaction: ' . $e->getMessage();
            $_SESSION['old'] = $_POST;
            $this->redirect('/transactions/' . $id . '/edit');
        }
    }

    public function delete(int $id): void {
        $this->requireAuth();
        // CSRF validation removed as per requirements

        $transaction = $this->transactionModel->find($id);

        if (!$transaction || $transaction['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/transactions');
            return;
        }

        try {
            $this->transactionModel->delete($id);
            $_SESSION['success'] = 'Transaction deleted successfully';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to delete transaction';
        }

        $this->redirect('/transactions');
    }
}
