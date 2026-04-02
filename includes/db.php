<?php
require_once __DIR__ . '/config.php';

class Database {
    private mysqli $conn;

    public function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        $this->conn->set_charset('utf8mb4');
    }

    public function getConnection(): mysqli {
        return $this->conn;
    }

    public function prepare(string $sql): mysqli_stmt {
        return $this->conn->prepare($sql);
    }

    public function query(string $sql): mysqli_result|bool {
        return $this->conn->query($sql);
    }

    public function lastInsertId(): int {
        return (int)$this->conn->insert_id;
    }
}

$db = new Database();
