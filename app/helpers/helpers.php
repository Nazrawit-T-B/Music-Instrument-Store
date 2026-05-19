<?php
// Input sanitization
function sanitize(mixed $input): string {
    return htmlspecialchars(
        strip_tags(trim((string) $input)),
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    );
}

// CSRF token generation & validation
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf_token(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }
}

// Auth guard
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
}

function require_admin(): void {
    require_login();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        http_response_code(403);
        die('Access denied.');
    }
}

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}
?>
