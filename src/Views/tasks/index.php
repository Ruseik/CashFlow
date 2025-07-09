<?php
// src/Views/tasks/index.php
?>
<h1>Task Management</h1>
<p>Active, uncompleted tasks grouped by importance and urgency:</p>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<div class="card mb-4 shadow">
    <div class="card-header bg-success text-white">Create New Task</div>
    <div class="card-body">
        <form method="post" action="/tasks" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="task_name" class="form-label">Task Name</label>
                <input type="text" class="form-control" id="task_name" name="task_name" required value="<?= htmlspecialchars($old['task_name'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="deadline_date" class="form-label">Deadline Date</label>
                <input type="date" class="form-control" id="deadline_date" name="deadline_date" required value="<?= htmlspecialchars($old['deadline_date'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label for="deadline_time" class="form-label">Deadline Time</label>
                <input type="time" class="form-control" id="deadline_time" name="deadline_time" required value="<?= htmlspecialchars($old['deadline_time'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex align-items-center gap-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="urgent_status" name="urgent_status" value="1" <?= !empty($old['urgent_status']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="urgent_status">Urgent</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="important_status" name="important_status" value="1" <?= !empty($old['important_status']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="important_status">Important</label>
                </div>
                <button type="submit" class="btn btn-success ms-2">Create Task</button>
            </div>
        </form>
    </div>
</div>
<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-danger mb-3 shadow">
            <div class="card-header bg-danger text-white">Important & Urgent</div>
            <div class="card-body">
                <?php if (!empty($groupedTasks['important_urgent'])): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($groupedTasks['important_urgent'] as $task): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($task['task_name']) ?></strong>
                                    <?php if (!empty($task['description'])): ?>
                                        <br><small><?= htmlspecialchars($task['description']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="/tasks/<?= $task['id'] ?>" class="btn btn-outline-primary btn-sm" title="View/Edit"><i class="bi bi-eye"></i></a>
                                    <form method="post" action="/tasks/<?= $task['id'] ?>/success" style="display:inline">
                                        <button type="submit" class="btn btn-outline-success btn-sm" title="Mark Successful" onclick="return confirm('Mark as successful?')"><i class="bi bi-check-circle"></i></button>
                                    </form>
                                    <form method="post" action="/tasks/<?= $task['id'] ?>/fail" style="display:inline">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Mark Failed" onclick="return confirm('Mark as failed?')"><i class="bi bi-x-circle"></i></button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <span class="text-muted">No tasks</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-warning mb-3 shadow">
            <div class="card-header bg-warning text-dark">Important & Not Urgent</div>
            <div class="card-body">
                <?php if (!empty($groupedTasks['important_not_urgent'])): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($groupedTasks['important_not_urgent'] as $task): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($task['task_name']) ?></strong>
                                    <?php if (!empty($task['description'])): ?>
                                        <br><small><?= htmlspecialchars($task['description']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="/tasks/<?= $task['id'] ?>" class="btn btn-outline-primary btn-sm" title="View/Edit"><i class="bi bi-eye"></i></a>
                                    <form method="post" action="/tasks/<?= $task['id'] ?>/success" style="display:inline">
                                        <button type="submit" class="btn btn-outline-success btn-sm" title="Mark Successful" onclick="return confirm('Mark as successful?')"><i class="bi bi-check-circle"></i></button>
                                    </form>
                                    <form method="post" action="/tasks/<?= $task['id'] ?>/fail" style="display:inline">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Mark Failed" onclick="return confirm('Mark as failed?')"><i class="bi bi-x-circle"></i></button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <span class="text-muted">No tasks</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-primary mb-3 shadow">
            <div class="card-header bg-primary text-white">Not Important & Urgent</div>
            <div class="card-body">
                <?php if (!empty($groupedTasks['not_important_urgent'])): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($groupedTasks['not_important_urgent'] as $task): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($task['task_name']) ?></strong>
                                    <?php if (!empty($task['description'])): ?>
                                        <br><small><?= htmlspecialchars($task['description']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="/tasks/<?= $task['id'] ?>" class="btn btn-outline-primary btn-sm" title="View/Edit"><i class="bi bi-eye"></i></a>
                                    <form method="post" action="/tasks/<?= $task['id'] ?>/success" style="display:inline">
                                        <button type="submit" class="btn btn-outline-success btn-sm" title="Mark Successful" onclick="return confirm('Mark as successful?')"><i class="bi bi-check-circle"></i></button>
                                    </form>
                                    <form method="post" action="/tasks/<?= $task['id'] ?>/fail" style="display:inline">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Mark Failed" onclick="return confirm('Mark as failed?')"><i class="bi bi-x-circle"></i></button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <span class="text-muted">No tasks</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-secondary mb-3 shadow">
            <div class="card-header bg-secondary text-white">Not Important & Not Urgent</div>
            <div class="card-body">
                <?php if (!empty($groupedTasks['not_important_not_urgent'])): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($groupedTasks['not_important_not_urgent'] as $task): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($task['task_name']) ?></strong>
                                    <?php if (!empty($task['description'])): ?>
                                        <br><small><?= htmlspecialchars($task['description']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="/tasks/<?= $task['id'] ?>" class="btn btn-outline-primary btn-sm" title="View/Edit"><i class="bi bi-eye"></i></a>
                                    <form method="post" action="/tasks/<?= $task['id'] ?>/success" style="display:inline">
                                        <button type="submit" class="btn btn-outline-success btn-sm" title="Mark Successful" onclick="return confirm('Mark as successful?')"><i class="bi bi-check-circle"></i></button>
                                    </form>
                                    <form method="post" action="/tasks/<?= $task['id'] ?>/fail" style="display:inline">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Mark Failed" onclick="return confirm('Mark as failed?')"><i class="bi bi-x-circle"></i></button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <span class="text-muted">No tasks</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
