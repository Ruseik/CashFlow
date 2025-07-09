<?php
// src/Models/Task.php

namespace Models;

require_once 'Model.php';

/**
 * Class Task
 * @property int $id
 * @property string $task_name
 * @property string|null $description
 * @property string $created_at
 * @property string|null $assigned_at
 * @property bool $active
 * @property bool|null $successful
 * @property string|null $successful_at
 * @property bool|null $delayed
 * @property string|null $delayed_at
 * @property bool|null $failed
 * @property string|null $failed_at
 * @property bool|null $urgent
 * @property bool|null $important
 * @property int $created_user
 * @property int|null $assigned_user
 * @property int|null $transaction_id
 */
class Task extends Model
{
    protected string $table = 'tasks';

    public function getAll()
    {
        $stmt = self::$db->query('SELECT * FROM tasks');
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all active, uncompleted tasks grouped by importance and urgency
     * @return array
     */
    public function getActiveUncompletedGrouped()
    {
        $sql = "SELECT * FROM tasks WHERE active_status = TRUE AND (successful_status IS NULL OR successful_status = FALSE) AND (failed_status IS NULL OR failed_status = FALSE)";
        $stmt = self::$db->query($sql);
        $tasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $groups = [
            'important_urgent' => [],
            'important_not_urgent' => [],
            'not_important_urgent' => [],
            'not_important_not_urgent' => []
        ];
        foreach ($tasks as $task) {
            if ($task['important_status'] && $task['urgent_status']) {
                $groups['important_urgent'][] = $task;
            } elseif ($task['important_status'] && !$task['urgent_status']) {
                $groups['important_not_urgent'][] = $task;
            } elseif (!$task['important_status'] && $task['urgent_status']) {
                $groups['not_important_urgent'][] = $task;
            } else {
                $groups['not_important_not_urgent'][] = $task;
            }
        }
        return $groups;
    }

    /**
     * Create a new task with name, deadline, user assignment, urgent and important status
     * @param string $name
     * @param string $deadline (Y-m-d H:i:s)
     * @param int $userId
     * @param bool $urgent
     * @param bool $important
     * @return bool
     */
    public function createTask(string $name, string $deadline, int $userId, bool $urgent, bool $important): bool
    {
        $sql = "INSERT INTO tasks (task_name, assigned_at, active_status, created_user, assigned_user, urgent_status, important_status) VALUES (:name, :deadline, TRUE, :created_user, :assigned_user, :urgent, :important)";
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':deadline' => $deadline,
            ':created_user' => $userId,
            ':assigned_user' => $userId,
            ':urgent' => $urgent ? 1 : 0,
            ':important' => $important ? 1 : 0
        ]);
    }

    /**
     * Get a single task by ID
     */
    public function getById(int $id): ?array
    {
        $stmt = self::$db->prepare('SELECT * FROM tasks WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $task = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $task ?: null;
    }

    /**
     * Mark a task as successful
     */
    public function markSuccessful(int $id): bool
    {
        $stmt = self::$db->prepare('UPDATE tasks SET successful_status = TRUE, successful_at = NOW(), active_status = FALSE WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Mark a task as failed
     */
    public function markFailed(int $id): bool
    {
        $stmt = self::$db->prepare('UPDATE tasks SET failed_status = TRUE, failed_at = NOW(), active_status = FALSE WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
