<?php
declare(strict_types=1);

namespace Controllers;

use Models\User;

class UserController extends Controller {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index(): void {
        $this->requireAuth();
        if (!($_SESSION['is_admin'] ?? false)) {
            $this->redirect('/');
            return;
        }
        $users = $this->userModel->findAll([], ['username' => 'ASC']);
        $this->render('users/index', [
            'users' => $users,
            'csrf_token' => $this->csrf()
        ]);
    }

    public function edit(int $id): void {
        $this->requireAuth();
        if (!($_SESSION['is_admin'] ?? false)) {
            $this->redirect('/');
            return;
        }
        $user = $this->userModel->find($id);
        if (!$user) {
            $this->redirect('/users');
            return;
        }
        $this->render('users/edit', [
            'user' => $user,
            'csrf_token' => $this->csrf()
        ]);
    }

    public function update(int $id): void {
        $this->requireAuth();
        $this->validateCsrf();
        if (!($_SESSION['is_admin'] ?? false)) {
            $this->redirect('/');
            return;
        }
        $user = $this->userModel->find($id);
        if (!$user) {
            $this->redirect('/users');
            return;
        }
        $isAdmin = isset($_POST['is_admin']) ? 1 : 0;
        $this->userModel->update($id, [
            'is_admin' => $isAdmin
        ]);
        $_SESSION['success'] = 'User updated successfully.';
        $this->redirect('/users');
    }

    public function delete(int $id): void {
        $this->requireAuth();
        $this->validateCsrf();
        if (!($_SESSION['is_admin'] ?? false)) {
            $this->redirect('/');
            return;
        }
        $user = $this->userModel->find($id);
        if (!$user) {
            $this->redirect('/users');
            return;
        }
        if ($user['id'] == $_SESSION['user_id']) {
            $_SESSION['error'] = 'You cannot delete your own account.';
            $this->redirect('/users');
            return;
        }
        $this->userModel->delete($id);
        $_SESSION['success'] = 'User deleted successfully.';
        $this->redirect('/users');
    }
}
