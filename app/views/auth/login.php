<?php require_once VIEW_PATH . 'partials/header.php' ?>

<?php
$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']);
?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <p><?= $error ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form id="signin" method="POST" action="/login">
    <?= csrf_field() ?>
    <fieldset>
        <legend>Sign In</legend>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required
               value="<?= sanitize($_SESSION['form_old']['email'] ?? '') ?>">
        <br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br><br>

        <input type="submit" value="Sign In">
        <p>Don't have an account? <a href="/register">Sign up here.</a></p>
    </fieldset>
</form>

<script src="/assets/javascript/signin.js"></script>

<?php require_once VIEW_PATH . 'partials/footer.php' ?>