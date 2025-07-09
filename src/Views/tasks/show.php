<?php
// src/Views/tasks/show.php
?>
<h1>Task Details</h1>
<a href="/tasks" class="btn btn-secondary mb-3">&larr; Back to Tasks</a>
<div class="card mb-4">
    <div class="card-body">
        <h4><?= htmlspecialchars($task['task_name']) ?></h4>
        <p><strong>Description:</strong> <?= htmlspecialchars($task['description'] ?? '—') ?></p>
        <p><strong>Deadline:</strong> <?= htmlspecialchars($task['assigned_at']) ?></p>
        <p><strong>Urgent:</strong> <?= $task['urgent_status'] ? 'Yes' : 'No' ?></p>
        <p><strong>Important:</strong> <?= $task['important_status'] ? 'Yes' : 'No' ?></p>
        <p><strong>Status:</strong> 
            <?php if ($task['successful_status']): ?>
                <span class="badge bg-success">Successful</span>
            <?php elseif ($task['failed_status']): ?>
                <span class="badge bg-danger">Failed</span>
            <?php else: ?>
                <span class="badge bg-warning text-dark">Active</span>
            <?php endif; ?>
        </p>
        <!-- Add more fields and edit form as needed -->
    </div>
</div>
