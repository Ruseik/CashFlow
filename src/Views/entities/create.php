<div class="row mb-4">
    <div class="col">
        <h2>Add Entity</h2>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="/entities" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control <?= isset($_SESSION['errors']['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>" required>
                <?php if (isset($_SESSION['errors']['name'])): ?>
                    <div class="invalid-feedback">
                        <?= htmlspecialchars($_SESSION['errors']['name']) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($_SESSION['old']['description'] ?? '') ?></textarea>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="show_in_basic_mode" name="show_in_basic_mode" <?= !empty($_SESSION['old']['show_in_basic_mode']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="show_in_basic_mode">
                    Show in Basic Mode
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="/entities" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script>
// Client-side form validation
(function () {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php
unset($_SESSION['errors']);
unset($_SESSION['old']);
?>
