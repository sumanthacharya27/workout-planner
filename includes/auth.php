<?php
require_once 'db.php';

class Auth {
    private $db;
    
    public function __construct($db) {
        $this->db = $db->getConnection();
    }
    
    public function register($email, $password, $name, $role = 'user') {
        $email = $this->db->real_escape_string($email);
        $name = $this->db->real_escape_string($name);
        $role = $this->db->real_escape_string($role);
        
        // Check if user exists
        $check = $this->db->query("SELECT id FROM users WHERE email = '$email'");
        if ($check->num_rows > 0) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user
        $sql = "INSERT INTO users (email, password, name, role) VALUES ('$email', '$hashedPassword', '$name', '$role')";
        if ($this->db->query($sql)) {
            return ['success' => true, 'message' => 'Registration successful', 'user_id' => $this->db->insert_id];
        } else {
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }

    public function login($email, $password) {
        $email = $this->db->real_escape_string($email);
        $result = $this->db->query("SELECT id, password, name, role FROM users WHERE email = '$email'");
        
        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
        
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $user['role'];
            return ['success' => true, 'message' => 'Login successful', 'role' => $user['role']];
        } else {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
    }

    public function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public function getCurrentUserRole() {
        return $_SESSION['user_role'] ?? 'user';
    }

    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Logged out'];
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function getCurrentUserName() {
        return $_SESSION['user_name'] ?? null;
    }
}

$auth = new Auth($db);
