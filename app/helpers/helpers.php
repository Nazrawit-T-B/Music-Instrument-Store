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

/**
 * Upload a product image file
 * Validates file type (jpg/png/webp), size (max 2MB), and MIME type
 * Saves with unique hash filename to /uploads/
 * 
 * @param array $file - $_FILES['fieldname']
 * @return string|false - stored path if successful, false on error
 */
function uploadProductImage(array $file): string|false {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("Upload error: " . $file['error']);
        return false;
    }

    // Allowed extensions
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    
    // Get file extension
    $fileName = $file['name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExt, $allowedExtensions)) {
        error_log("Invalid file extension: {$fileExt}");
        return false;
    }

    // Check file size (2MB max)
    $maxSize = 2 * 1024 * 1024; // 2MB in bytes
    if ($file['size'] > $maxSize) {
        error_log("File too large: {$file['size']} bytes");
        return false;
    }

    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes)) {
        error_log("Invalid MIME type: {$mimeType}");
        return false;
    }

    // Create unique filename using hash
    $hash = hash_file('sha256', $file['tmp_name']);
    $uniqueFileName = substr($hash, 0, 12) . '.' . $fileExt;
    
    // Ensure uploads directory exists
    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }

    $targetPath = UPLOAD_PATH . $uniqueFileName;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        error_log("Failed to move uploaded file to {$targetPath}");
        return false;
    }

    // Set proper permissions
    chmod($targetPath, 0644);

    // Return relative path for storage in database
    return '/uploads/' . $uniqueFileName;
}
?>
