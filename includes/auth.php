<?php
require_once 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

class Auth {
    private mysqli $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function register(string $email, string $password, string $name): array {
        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        $name = sanitizeText($name, 100);

        if (!$email || strlen($password) < 8 || $name === '') {
            return ['success' => false, 'message' => 'Invalid registration data'];
        }

        $check = $this->db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare('INSERT INTO users (email, password, name) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $email, $hash, $name);
        $stmt->execute();

        return ['success' => true, 'message' => 'Registration successful', 'user_id' => (int)$this->db->insert_id];
    }

    public function login(string $email, string $password): array {
        $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        if (!$email || $password === '') {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        $stmt = $this->db->prepare('SELECT id, password, name FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['last_activity'] = time();

        return ['success' => true, 'message' => 'Login successful'];
    }

    public function logout(): array {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool)$params['secure'], (bool)$params['httponly']);
        }
        session_destroy();
        return ['success' => true, 'message' => 'Logged out'];
    }

    public function isLoggedIn(): bool {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return false;
        }

        $lastActivity = $_SESSION['last_activity'] ?? 0;
        if ((time() - (int)$lastActivity) > SESSION_TIMEOUT) {
            $this->logout();
            return false;
        }

        $_SESSION['last_activity'] = time();
        return true;
    }

    public function getCurrentUserId(): ?int {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public function getCurrentUserName(): ?string {
        return $_SESSION['user_name'] ?? null;
    }
}

$auth = new Auth($db);
