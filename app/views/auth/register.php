<?php require_once VIEW_PATH . 'partials/header.php' ?>

<?php
$errors = $_SESSION['form_errors'] ?? [];
$old    = $_SESSION['form_old']    ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_old']);
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <p><?= $error ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form id="signup" method="POST" action="/register">
        <?= csrf_field() ?>
        <fieldset>
            <legend>Create Account</legend>

            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required
                       value="<?= sanitize($old['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required
                       value="<?= sanitize($old['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <small class="password-hint">Must be at least 8 characters with letters and numbers</small>
            </div>

            <div class="form-group">
                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm" required>
            </div>

            <input type="submit" value="Create Account" class="signup-button">
            <p class="signin-link">Already have an account? <a href="/login">Sign in here</a></p>
        </fieldset>
    </form>
</div>

<script src="/Music-Instrument-Store/assets/javascript/signup.js"></script>

<?php require_once VIEW_PATH . 'partials/footer.php' ?>