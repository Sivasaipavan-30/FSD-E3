<?php
// api/events.php
require_once 'db.php';

$action = $_GET['action'] ?? 'list';
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

if ($action === 'list') {
    $stmt = $pdo->query("SELECT * FROM events ORDER BY datetime ASC");
    $events = $stmt->fetchAll();
    sendResponse(true, 'Events fetched', $events);
}

if ($action === 'get') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    if ($event) {
        sendResponse(true, 'Event details', $event);
    } else {
        sendResponse(false, 'Event not found');
    }
}

// Admin only actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$is_admin) {
        sendResponse(false, 'Unauthorized access');
    }

    if ($action === 'create') {
        $name = $_POST['name'] ?? '';
        $dept = $_POST['department'] ?? '';
        $datetime = $_POST['datetime'] ?? '';
        $venue = $_POST['venue'] ?? '';
        $price = $_POST['price'] ?? 0;
        $seats = $_POST['seats'] ?? 0;
        $desc = $_POST['description'] ?? '';

        if (empty($name) || empty($datetime) || empty($seats)) {
            sendResponse(false, 'Name, Date, and Seats are required');
        }

        $stmt = $pdo->prepare("INSERT INTO events (name, department, datetime, venue, price, seats, available_seats, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$name, $dept, $datetime, $venue, $price, $seats, $seats, $desc]);
            logAdminAction($pdo, 'CREATE_EVENT', "Created event: $name");
            sendResponse(true, 'Event created successfully');
        } catch (PDOException $e) {
            sendResponse(false, 'Failed to create event: ' . $e->getMessage());
        }
    }

    if ($action === 'update') {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $dept = $_POST['department'] ?? '';
        $datetime = $_POST['datetime'] ?? '';
        $venue = $_POST['venue'] ?? '';
        $price = $_POST['price'] ?? 0;
        $seats = $_POST['seats'] ?? 0;
        $desc = $_POST['description'] ?? '';

        if (!$id) sendResponse(false, 'Event ID is required');

        // Fetch current event to handle seats
        $stmt = $pdo->prepare("SELECT seats, available_seats FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $event = $stmt->fetch();
        if (!$event) sendResponse(false, 'Event not found');

        // Simple seat logic: if total seats changed, adjust available seats relative to the change
        $seatDiff = $seats - $event['seats'];
        $newAvailable = $event['available_seats'] + $seatDiff;
        if ($newAvailable < 0) $newAvailable = 0; // Don't let it go negative

        $stmt = $pdo->prepare("UPDATE events SET name=?, department=?, datetime=?, venue=?, price=?, seats=?, available_seats=?, description=? WHERE id=?");
        try {
            $stmt->execute([$name, $dept, $datetime, $venue, $price, $seats, $newAvailable, $desc, $id]);
            logAdminAction($pdo, 'UPDATE_EVENT', "Updated event ID: $id ($name)");
            sendResponse(true, 'Event updated successfully');
        } catch (PDOException $e) {
            sendResponse(false, 'Failed to update event: ' . $e->getMessage());
        }
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        if (!$id) sendResponse(false, 'Event ID is required');

        try {
            // First, double check if event exists
            $check = $pdo->prepare("SELECT id FROM events WHERE id = ?");
            $check->execute([$id]);
            if (!$check->fetch()) {
                sendResponse(false, 'Event not found in database');
            }

            $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
            if ($stmt->execute([$id])) {
                logAdminAction($pdo, 'DELETE_EVENT', "Deleted event ID: $id");
                sendResponse(true, 'Event deleted successfully');
            } else {
                sendResponse(false, 'Failed to execute delete query');
            }
        } catch (PDOException $e) {
            // Check for foreign key constraint failures
            if ($e->getCode() == '23000') {
                sendResponse(false, 'Cannot delete event: It has active bookings or feedback associated with it.');
            } else {
                sendResponse(false, 'Database Error: ' . $e->getMessage());
            }
        }
    }
}
?>
