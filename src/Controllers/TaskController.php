<?php
// src/Controllers/TaskController.php

namespace Controllers;

use Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $taskModel = new \Models\Task();
        $groupedTasks = $taskModel->getActiveUncompletedGrouped();
        $this->render('tasks/index', ['groupedTasks' => $groupedTasks]);
    }

    public function store()
    {
        $errors = [];
        $success = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['task_name'] ?? '');
            $date = trim($_POST['deadline_date'] ?? '');
            $time = trim($_POST['deadline_time'] ?? '');
            $urgent = isset($_POST['urgent_status']) ? true : false;
            $important = isset($_POST['important_status']) ? true : false;
            $userId = $_SESSION['user_id'] ?? null;
            if ($name === '') {
                $errors[] = 'Task name is required.';
            }
            if ($date === '' || $time === '') {
                $errors[] = 'Deadline date and time are required.';
            }
            if (!$userId) {
                $errors[] = 'User not logged in.';
            }
            $deadline = $date . ' ' . $time;
            if (empty($errors)) {
                $taskModel = new \Models\Task();
                $success = $taskModel->createTask($name, $deadline, $userId, $urgent, $important);
                if ($success) {
                    $_SESSION['success'] = 'Task created successfully!';
                    header('Location: /tasks');
                    exit;
                } else {
                    $errors[] = 'Failed to create task.';
                }
            }
        }
        // Re-render the page with errors
        $taskModel = new \Models\Task();
        $groupedTasks = $taskModel->getActiveUncompletedGrouped();
        $this->render('tasks/index', [
            'groupedTasks' => $groupedTasks,
            'errors' => $errors,
            'old' => $_POST
        ]);
    }

    public function show($id)
    {
        $taskModel = new \Models\Task();
        $task = $taskModel->getById((int)$id);
        if (!$task) {
            http_response_code(404);
            echo 'Task not found';
            exit;
        }
        $this->render('tasks/show', ['task' => $task]);
    }

    public function markSuccessful($id)
    {
        $taskModel = new \Models\Task();
        $taskModel->markSuccessful((int)$id);
        $_SESSION['success'] = 'Task marked as successful!';
        header('Location: /tasks');
        exit;
    }

    public function markFailed($id)
    {
        $taskModel = new \Models\Task();
        $taskModel->markFailed((int)$id);
        $_SESSION['success'] = 'Task marked as failed!';
        header('Location: /tasks');
        exit;
    }
}
