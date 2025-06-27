<?php
declare(strict_types=1);

namespace Controllers;

use Models\Entity;

class EntityController extends Controller {
    private Entity $entityModel;

    public function __construct() {
        $this->entityModel = new Entity();
    }

    public function index(): void {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $entities = $this->entityModel->findByUser($userId, false);
        $this->render('entities/index', [
            'entities' => $entities,
            'csrf_token' => $this->csrf()
        ]);
    }

    public function create(): void {
        $this->requireAuth();
        $this->render('entities/create', [
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
            $this->redirect('/entities/create');
            return;
        }
        $this->entityModel->create([
            'name' => $name,
            'description' => $description,
            'user_id' => $userId,
            'show_in_basic_mode' => $showInBasic
        ]);
        $_SESSION['success'] = 'Entity created successfully.';
        $this->redirect('/entities');
    }

    public function edit(int $id): void {
        $this->requireAuth();
        $entity = $this->entityModel->find($id);
        if (!$entity || $entity['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/entities');
            return;
        }
        $this->render('entities/edit', [
            'entity' => $entity,
            'csrf_token' => $this->csrf()
        ]);
    }

    public function update(int $id): void {
        $this->requireAuth();
        $this->validateCsrf();
        $entity = $this->entityModel->find($id);
        if (!$entity || $entity['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/entities');
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
            $this->redirect('/entities/' . $id . '/edit');
            return;
        }
        $this->entityModel->update($id, [
            'name' => $name,
            'description' => $description,
            'show_in_basic_mode' => $showInBasic
        ]);
        $_SESSION['success'] = 'Entity updated successfully.';
        $this->redirect('/entities');
    }

    public function delete(int $id): void {
        $this->requireAuth();
        $this->validateCsrf();
        $entity = $this->entityModel->find($id);
        if (!$entity || $entity['user_id'] !== $_SESSION['user_id']) {
            $this->redirect('/entities');
            return;
        }
        $this->entityModel->delete($id);
        $_SESSION['success'] = 'Entity deleted successfully.';
        $this->redirect('/entities');
    }
}
