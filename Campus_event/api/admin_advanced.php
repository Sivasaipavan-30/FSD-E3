<?php
require_once 'db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($action === 'audit_logs') {
    try {
        $stmt = $pdo->query("
            SELECT al.*, u.name as admin_name 
            FROM audit_logs al
            JOIN users u ON al.admin_id = u.id
            ORDER BY al.created_at DESC 
            LIMIT 50
        ");
        $logs = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $logs]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} elseif ($action === 'feedback_details') {
    $eventId = $_GET['event_id'] ?? 0;
    try {
        $stmt = $pdo->prepare("
            SELECT f.*, u.name as user_name 
            FROM event_feedback f
            JOIN users u ON f.user_id = u.id
            WHERE f.event_id = ?
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$eventId]);
        $feedback = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM event_feedback WHERE event_id = ?");
        $stmt->execute([$eventId]);
        $stats = $stmt->fetch();

        echo json_encode(['success' => true, 'data' => $feedback, 'stats' => $stats]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
