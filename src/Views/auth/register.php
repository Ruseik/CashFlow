<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-header">
                <h4 class="card-title mb-0">Register</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/register" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control <?= isset($_SESSION['errors']['username']) ? 'is-invalid' : '' ?>"
                               id="username" name="username" value="<?= htmlspecialchars($_SESSION['old']['username'] ?? '') ?>" required>
                        <?php if (isset($_SESSION['errors']['username'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($_SESSION['errors']['username']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control <?= isset($_SESSION['errors']['email']) ? 'is-invalid' : '' ?>"
                               id="email" name="email" value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>" required>
                        <?php if (isset($_SESSION['errors']['email'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($_SESSION['errors']['email']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control <?= isset($_SESSION['errors']['password']) ? 'is-invalid' : '' ?>"
                               id="password" name="password" required>
                        <?php if (isset($_SESSION['errors']['password'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($_SESSION['errors']['password']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control <?= isset($_SESSION['errors']['password_confirm']) ? 'is-invalid' : '' ?>"
                               id="password_confirm" name="password_confirm" required>
                        <?php if (isset($_SESSION['errors']['password_confirm'])): ?>
                            <div class="invalid-feedback">
                                <?= htmlspecialchars($_SESSION['errors']['password_confirm']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">
                    Already have an account? <a href="/login">Login</a>
                </p>
            </div>
        </div>
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
// Clear session data
unset($_SESSION['errors']);
unset($_SESSION['old']);
?>
