<div class="row mb-4">
    <div class="col">
        <h2>Edit User</h2>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="/users/<?= $user['id'] ?>" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin" <?= $user['is_admin'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_admin">
                    Admin
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="/users" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
