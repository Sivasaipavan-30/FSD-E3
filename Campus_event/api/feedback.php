<?php
require_once 'db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $eventId = $_POST['event_id'];
    $userId = $_SESSION['user_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    try {
        // Check if user actually attended (checked in)
        $stmt = $pdo->prepare("
            SELECT ba.is_checked_in 
            FROM booking_attendees ba
            JOIN bookings b ON ba.booking_id = b.id
            WHERE b.event_id = ? AND b.user_id = ? AND ba.is_checked_in = 1
            LIMIT 1
        ");
        $stmt->execute([$eventId, $userId]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'You can only rate events you attended.']);
            exit;
        }

        // Upsert feedback
        $stmt = $pdo->prepare("
            INSERT INTO event_feedback (event_id, user_id, rating, comment)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)
        ");
        $stmt->execute([$eventId, $userId, $rating, $comment]);

        echo json_encode(['success' => true, 'message' => 'Feedback submitted!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} elseif ($action === 'get_stats') {
    $eventId = $_GET['event_id'];
    try {
        $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM event_feedback WHERE event_id = ?");
        $stmt->execute([$eventId]);
        $stats = $stmt->fetch();
        echo json_encode(['success' => true, 'stats' => $stats]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
