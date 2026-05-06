<?php
// diag.php - diagnostic script
require_once 'api/db.php';
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? 'NULL';
$userRole = $_SESSION['user_role'] ?? 'NULL';

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM bookings WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id'] ?? 0]);
$count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
$totalCount = $stmt->fetch()['count'];

echo json_encode([
    'session_user_id' => $userId,
    'session_user_role' => $userRole,
    'user_bookings_count' => $count,
    'total_bookings_count' => $totalCount
]);
