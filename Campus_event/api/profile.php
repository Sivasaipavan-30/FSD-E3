<?php
// api/profile.php
require_once 'db.php';

$action = $_GET['action'] ?? '';

if (!isset($_SESSION['user_id'])) {
    sendResponse(false, 'Unauthorized');
}

$userId = $_SESSION['user_id'];

if ($action === 'update') {
    $name = $_POST['name'] ?? '';
    $dept = $_POST['department'] ?? '';

    if (empty($name)) {
        sendResponse(false, 'Name cannot be empty');
    }

    try {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, department = ? WHERE id = ?");
        $stmt->execute([$name, $dept, $userId]);
        
        $_SESSION['user_name'] = $name;
        
        sendResponse(true, 'Profile updated successfully', ['name' => $name, 'department' => $dept]);
    } catch (PDOException $e) {
        sendResponse(false, 'Update failed: ' . $e->getMessage());
    }
}

if ($action === 'get') {
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, department, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if ($user) {
            sendResponse(true, 'Profile loaded', $user);
        } else {
            sendResponse(false, 'User not found');
        }
    } catch (PDOException $e) {
        sendResponse(false, 'Database error: ' . $e->getMessage());
    }
}
?>
