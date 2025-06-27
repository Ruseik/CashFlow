<?php
declare(strict_types=1);

namespace Controllers;

use Models\Mode;

class ModeController extends Controller {
    private Mode $modeModel;

    public function __construct() {
        $this->modeModel = new Mode();
    }

    public function index(): void {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $modes = $this->modeModel->findByUser($userId, false);
        $this->render('modes/index', [
            'modes' => $modes,
            'csrf_token' => $this->csrf()
        ]);
    }

    public function create(): void {
        $this->requireAuth();
        $this->render('modes/create', [
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
            $this->redirect('/modes/create');
            return;
        }
        $this->modeModel->create([
            'name' => $name,
            'description' => $description,
            'user_id' => $userId,
            'show_in_basic_mode' => $showInBasic
        ]);
        $_SESSION['success'] = 'Mode created successfully.';
        $this->redirect('/modes');
    }

    public function edit(int $id): void {
        $this->requireAuth();
        $mode = $this->modeModel->find($id);
        if (!$mode || $mode['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/modes');
            return;
        }
        $this->render('modes/edit', [
            'mode' => $mode,
            'csrf_token' => $this->csrf()
        ]);
    }

    public function update(int $id): void {
        $this->requireAuth();
        $this->validateCsrf();
        $mode = $this->modeModel->find($id);
        if (!$mode || $mode['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/modes');
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
            $this->redirect('/modes/' . $id . '/edit');
            return;
        }
        $this->modeModel->update($id, [
            'name' => $name,
            'description' => $description,
            'show_in_basic_mode' => $showInBasic
        ]);
        $_SESSION['success'] = 'Mode updated successfully.';
        $this->redirect('/modes');
    }

    public function delete(int $id): void {
        $this->requireAuth();
        $this->validateCsrf();
        $mode = $this->modeModel->find($id);
        if (!$mode || $mode['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/modes');
            return;
        }
        $this->modeModel->delete($id);
        $_SESSION['success'] = 'Mode deleted successfully.';
        $this->redirect('/modes');
    }
}
