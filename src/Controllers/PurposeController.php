<?php
declare(strict_types=1);

namespace Controllers;

use Models\Purpose;

class PurposeController extends Controller {
    private Purpose $purposeModel;

    public function __construct() {
        $this->purposeModel = new Purpose();
    }

    public function index(): void {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $purposes = $this->purposeModel->findByUser($userId, false);
        $this->render('purposes/index', [
            'purposes' => $purposes,
            'csrf_token' => $this->csrf()
        ]);
    }

    public function create(): void {
        $this->requireAuth();
        $this->render('purposes/create', [
            'csrf_token' => $this->csrf()
        ]);
    }

    public function store(): void {
        $this->requireAuth();
        $this->validateCsrf();
        $userId = $_SESSION['user_id'];
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $showInBasic = isset($_POST['show_in_basic_mode']) ? 1 : 0;
        $errors = [];
        if (strlen($name) < 2) {
            $errors['name'] = 'Name must be at least 2 characters.';
        }
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/purposes/create');
            return;
        }
        $this->purposeModel->create([
            'name' => $name,
            'description' => $description,
            'user_id' => $userId,
            'show_in_basic_mode' => $showInBasic
        ]);
        $_SESSION['success'] = 'Purpose created successfully.';
        $this->redirect('/purposes');
    }

    public function edit(int $id): void {
        $this->requireAuth();
        $purpose = $this->purposeModel->find($id);
        if (!$purpose || $purpose['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/purposes');
            return;
        }
        $this->render('purposes/edit', [
            'purpose' => $purpose,
            'csrf_token' => $this->csrf()
        ]);
    }

    public function update(int $id): void {
        $this->requireAuth();
        $this->validateCsrf();
        $purpose = $this->purposeModel->find($id);
        if (!$purpose || $purpose['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/purposes');
            return;
        }
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $showInBasic = isset($_POST['show_in_basic_mode']) ? 1 : 0;
        $errors = [];
        if (strlen($name) < 2) {
            $errors['name'] = 'Name must be at least 2 characters.';
        }
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/purposes/' . $id . '/edit');
            return;
        }
        $this->purposeModel->update($id, [
            'name' => $name,
            'description' => $description,
            'show_in_basic_mode' => $showInBasic
        ]);
        $_SESSION['success'] = 'Purpose updated successfully.';
        $this->redirect('/purposes');
    }

    public function delete(int $id): void {
        $this->requireAuth();
        $this->validateCsrf();
        $purpose = $this->purposeModel->find($id);
        if (!$purpose || $purpose['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/purposes');
            return;
        }
        $this->purposeModel->delete($id);
        $_SESSION['success'] = 'Purpose deleted successfully.';
        $this->redirect('/purposes');
    }
}
