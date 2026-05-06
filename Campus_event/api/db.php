<?php
session_start();
// api/db.php - Database connection using PDO
/**
 * This file establishes a secure connection to the MySQL database.
 * We use PDO (PHP Data Objects) because it is more secure than mysqli 
 * and helps prevent SQL injection with prepared statements.
 */

// Database configuration settings
$host = 'localhost';
$db   = 'campus_event_db';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // In production, don't show the full error
     die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
}

// Helper to return JSON responses
function sendResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Helper to log admin actions
function logAdminAction($pdo, $action, $details = '') {
    if (!isset($_SESSION['user_id'])) return;
    try {
        $stmt = $pdo->prepare("INSERT INTO audit_logs (admin_id, action, details) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $action, $details]);
    } catch (Exception $e) {
        // Fail silently to not break the main flow
    }
}

// CSRF / Auth check could go here later
?>
