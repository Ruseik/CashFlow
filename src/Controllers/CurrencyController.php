<?php
declare(strict_types=1);

namespace Controllers;

use Models\Currency;

class CurrencyController extends Controller {
    private Currency $currencyModel;

    public function __construct() {
        $this->currencyModel = new Currency();
    }

    public function index(): void {
        $this->requireAuth();
        $currencies = $this->currencyModel->getAllActive(false);
        $this->render('currencies/index', [
            'currencies' => $currencies,
            'csrf_token' => $this->csrf()
        ]);
    }

    public function create(): void {
        $this->requireAuth();
        $this->render('currencies/create', [
            'csrf_token' => $this->csrf()
        ]);
    }

    public function store(): void {
        $this->requireAuth();
        $this->validateCsrf();
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $symbol = trim($_POST['symbol'] ?? '');
        $showInBasic = isset($_POST['show_in_basic_mode']) ? 1 : 0;
        $errors = [];
        if (strlen($code) < 2 || strlen($code) > 5) {
            $errors['code'] = 'Currency code must be 2-5 characters.';
        }
        if (strlen($name) < 2) {
            $errors['name'] = 'Name must be at least 2 characters.';
        }
        if (!empty($this->currencyModel->findByCode($code))) {
            $errors['code'] = 'Currency code already exists.';
        }
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/currencies/create');
            return;
        }
        $this->currencyModel->create([
            'code' => $code,
            'name' => $name,
            'symbol' => $symbol,
            'show_in_basic_mode' => $showInBasic
        ]);
        $_SESSION['success'] = 'Currency created successfully.';
        $this->redirect('/currencies');
    }

    public function edit(int $id): void {
        $this->requireAuth();
        $currency = $this->currencyModel->find($id);
        if (!$currency) {
            $this->redirect('/currencies');
            return;
        }
        $this->render('currencies/edit', [
            'currency' => $currency,
            'csrf_token' => $this->csrf()
        ]);
    }

    public function update(int $id): void {
        $this->requireAuth();
        $this->validateCsrf();
        $currency = $this->currencyModel->find($id);
        if (!$currency) {
            $this->redirect('/currencies');
            return;
        }
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $symbol = trim($_POST['symbol'] ?? '');
        $showInBasic = isset($_POST['show_in_basic_mode']) ? 1 : 0;
        $errors = [];
        if (strlen($code) < 2 || strlen($code) > 5) {
            $errors['code'] = 'Currency code must be 2-5 characters.';
        }
        if (strlen($name) < 2) {
            $errors['name'] = 'Name must be at least 2 characters.';
        }
        $existing = $this->currencyModel->findByCode($code);
        if (!empty($existing) && $existing['id'] != $id) {
            $errors['code'] = 'Currency code already exists.';
        }
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/currencies/' . $id . '/edit');
            return;
        }
        $this->currencyModel->update($id, [
            'code' => $code,
            'name' => $name,
            'symbol' => $symbol,
            'show_in_basic_mode' => $showInBasic
        ]);
        $_SESSION['success'] = 'Currency updated successfully.';
        $this->redirect('/currencies');
    }

    public function delete(int $id): void {
        $this->requireAuth();
        $this->validateCsrf();
        $currency = $this->currencyModel->find($id);
        if (!$currency) {
            $this->redirect('/currencies');
            return;
        }
        $this->currencyModel->delete($id);
        $_SESSION['success'] = 'Currency deleted successfully.';
        $this->redirect('/currencies');
    }
}
