<?php
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'list';

if ($action === 'toggle') {
    $eventId = $_POST['event_id'] ?? 0;
    try {
        $stmt = $pdo->prepare("SELECT id FROM event_wishlist WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$userId, $eventId]);
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("DELETE FROM event_wishlist WHERE user_id = ? AND event_id = ?");
            $stmt->execute([$userId, $eventId]);
            echo json_encode(['success' => true, 'message' => 'Removed from wishlist', 'is_saved' => false]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO event_wishlist (user_id, event_id) VALUES (?, ?)");
            $stmt->execute([$userId, $eventId]);
            echo json_encode(['success' => true, 'message' => 'Added to wishlist', 'is_saved' => true]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} elseif ($action === 'list') {
    try {
        $stmt = $pdo->prepare("
            SELECT e.* 
            FROM event_wishlist w
            JOIN events e ON w.event_id = e.id
            WHERE w.user_id = ?
        ");
        $stmt->execute([$userId]);
        $wishlist = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $wishlist]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} elseif ($action === 'status') {
    // Get all event IDs in wishlist for current user
    try {
        $stmt = $pdo->prepare("SELECT event_id FROM event_wishlist WHERE user_id = ?");
        $stmt->execute([$userId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo json_encode(['success' => true, 'data' => $ids]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
