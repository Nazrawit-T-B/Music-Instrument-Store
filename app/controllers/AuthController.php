<?php
class AuthController {
    private UserModel $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // Registration

    public function showRegister(): void {
        require_once VIEW_PATH . 'auth/register.php';
    }

    public function register(): void {
        verify_csrf_token();

        $name     = sanitize($_POST['name']     ?? '');
        $email    = sanitize($_POST['email']    ?? '');
        $password =          $_POST['password'] ?? '';
        $confirm  =          $_POST['confirm']  ?? '';

        $errors = $this->validateRegistration($name, $email, $password, $confirm);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = compact('name', 'email');
            header('Location: /register');
            exit;
        }

        $userId = $this->userModel->create($name, $email, $password);
        $this->startUserSession($userId, $name);
        header('Location: /home');
        exit;
    }

    private function validateRegistration(
        string $name, string $email,
        string $password, string $confirm
    ): array {
        $errors = [];

        if (strlen($name) < 2) {
            $errors[] = 'Name must be at least 2 characters.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if ($this->userModel->emailExists($email)) {
            $errors[] = 'An account with that email already exists.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        return $errors;
    }

    // Login

    public function showLogin(): void {
        require_once VIEW_PATH . 'auth/login.php';
    }

    public function login(): void {
        verify_csrf_token();

        $email    = sanitize($_POST['email']    ?? '');
        $password =           $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        // prevents timing-based user enumeration
        $hash         = $user['password'] ?? '$2y$12$invaliddummyhashtopreventtiming';
        $passwordValid = $this->userModel->verifyPassword($password, $hash);

        if (!$user || !$passwordValid) {
            $_SESSION['form_errors'] = ['Invalid email or password.'];
            header('Location: /login');
            exit;
        }
        
        if (class_exists('CartC')) {
            $cartController = new CartC();
            $cartController->mergeCart((int)$user['id']);
        }

        session_regenerate_id(true);
        $this->startUserSession((int)$user['id'], $user['name']);

        header('Location: /home');
        exit;
    }

    // Logout

    public function logout(): void {
        $_SESSION = [];
        session_destroy();

        // Expire the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        header('Location: /login');
        exit;
    }

    // Shared

    private function startUserSession(int $id, string $name): void {
        $_SESSION['user_id']   = $id;
        $_SESSION['user_name'] = $name;
    }
}
?>
