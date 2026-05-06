<?php
// api/broadcast.php
require_once 'db.php';
require_once 'mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['event_id'] ?? 0;
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (!$eventId || !$subject || !$message) {
        sendResponse(false, 'All fields are required');
    }

    // Check if user is admin
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        sendResponse(false, 'Unauthorized');
    }

    try {
        // Get all unique student emails registered for this event
        $stmt = $pdo->prepare("
            SELECT DISTINCT u.email, u.name 
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            WHERE b.event_id = ?
        ");
        $stmt->execute([$eventId]);
        $students = $stmt->fetchAll();

        // Also get individual attendee emails if any
        $stmt = $pdo->prepare("
            SELECT DISTINCT email, name 
            FROM booking_attendees ba
            JOIN bookings b ON ba.booking_id = b.id
            WHERE b.event_id = ? AND email != 'N/A'
        ");
        $stmt->execute([$eventId]);
        $attendees = $stmt->fetchAll();

        $allRecipients = array_merge($students, $attendees);
        // De-duplicate by email
        $uniqueRecipients = [];
        foreach ($allRecipients as $r) {
            $uniqueRecipients[$r['email']] = $r['name'];
        }

        $count = 0;
        foreach ($uniqueRecipients as $email => $name) {
            $personalizedMessage = "<h2>Hello $name,</h2>";
            $personalizedMessage .= "<p>" . nl2br(htmlspecialchars($message)) . "</p>";
            $personalizedMessage .= "<hr><p style='font-size:0.8rem; color:#666;'>This is an official announcement from Smart Campus Events regarding an event you are registered for.</p>";
            
            $res = sendEmail($email, $subject, $personalizedMessage, true);
            if ($res['success']) $count++;
        }

        sendResponse(true, "Broadcast sent successfully to $count recipients!");
        logAdminAction($pdo, 'BROADCAST_SENT', "Subject: $subject to $count students (Event ID: $eventId)");
    } catch (Exception $e) {
        sendResponse(false, 'Broadcast failed: ' . $e->getMessage());
    }
}
?>
