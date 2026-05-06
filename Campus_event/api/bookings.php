<?php
// api/bookings.php
/**
 * Booking Handler
 * Handles ticket bookings, seat inventory management, and history.
 * Uses SQL Transactions to ensure data integrity.
 */
require_once 'db.php';

$action = $_GET['action'] ?? 'list';
$userId = $_SESSION['user_id'] ?? null;
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

if (!$userId) {
    sendResponse(false, 'Please login to continue');
}

if ($is_admin && $action === 'book') {
    sendResponse(false, 'Administrators are not permitted to book events.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'book') {
    $eventId = $_POST['event_id'] ?? 0;
    $numTickets = $_POST['tickets'] ?? 1;
    $attendeesByJson = $_POST['attendees'] ?? '[]';
    $attendees = json_decode($attendeesByJson, true) ?: [];

    // Check event availability
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();

    if (!$event) {
        sendResponse(false, 'Event not found');
    }

    if ($event['available_seats'] < $numTickets) {
        sendResponse(false, 'Not enough seats available');
    }

    $totalPrice = $event['price'] * $numTickets;

    try {
        $pdo->beginTransaction();

        // 1. Create booking
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, event_id, tickets, total_price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $eventId, $numTickets, $totalPrice]);
        $bookingId = $pdo->lastInsertId();

        // 2. Add individual attendee details
        if (!empty($attendees)) {
            $stmt = $pdo->prepare("INSERT INTO booking_attendees (booking_id, name, email, department, vtu_no, reg_no) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($attendees as $person) {
                $pEmail = $person['email'] ?? '';
                if (!empty($pEmail) && !filter_var($pEmail, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Invalid email format for attendee: " . ($person['name'] ?? 'Unknown'));
                }
                
                $stmt->execute([
                    $bookingId, 
                    $person['name'] ?? 'N/A', 
                    $pEmail ?: 'N/A',
                    $person['dept'] ?? 'N/A', 
                    $person['vtu'] ?? 'N/A', 
                    $person['reg'] ?? 'N/A'
                ]);
            }
        }

        // 3. Reduce available seats
        $stmt = $pdo->prepare("UPDATE events SET available_seats = available_seats - ? WHERE id = ?");
        $stmt->execute([$numTickets, $eventId]);

        $pdo->commit();

        // Send Confirmation Email
        try {
            require_once 'mailer.php';
            
            $stmtUser = $pdo->prepare("SELECT email, name FROM users WHERE id = ?");
            $stmtUser->execute([$userId]);
            $user = $stmtUser->fetch();
            
            if ($user && !empty($user['email'])) {
                $to = $user['email'];
                $subject = "Booking Confirmed: " . $event['name'];
                
                $receiptUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . str_replace('api/bookings.php', 'receipt.php', $_SERVER['PHP_SELF']) . "?id=" . $bookingId;

                $message = "<h2>Hello " . htmlspecialchars($user['name']) . "!</h2>";
                $message .= "<p>CONGRATULATIONS! Your booking for <b>" . htmlspecialchars($event['name']) . "</b> has been successfully registered.</p>";
                $message .= "<h3>--- BOOKING DETAILS ---</h3>";
                $message .= "<ul>";
                $message .= "<li><b>Event:</b> " . htmlspecialchars($event['name']) . "</li>";
                $message .= "<li><b>Tickets:</b> " . $numTickets . "</li>";
                $message .= "<li><b>Total Price:</b> ₹" . $totalPrice . "</li>";
                $message .= "<li><b>Date & Time:</b> " . date('F j, Y, g:i a', strtotime($event['datetime'])) . "</li>";
                $message .= "<li><b>Venue:</b> " . htmlspecialchars($event['venue']) . "</li>";
                $message .= "</ul>";
                
                $message .= "<h3>--- YOUR RECEIPT ---</h3>";
                $message .= "<p>You can download your official e-receipt here:<br>";
                $message .= "<a href='$receiptUrl'>Download E-Receipt</a></p>";
                
                $message .= "<p>Thank you for using Smart Campus Events!</p>";
                
                sendEmail($to, $subject, $message, true);
            }

            // --- Send individual emails to each attendee ---
            if (!empty($attendees)) {
                foreach ($attendees as $person) {
                    if (!empty($person['email'])) {
                        $attendeeSubject = "Your Ticket for " . $event['name'];
                        $attendeeMsg = "<h2>Hello " . htmlspecialchars($person['name']) . "!</h2>";
                        $attendeeMsg .= "<p>A ticket has been booked for you for the event: <b>" . htmlspecialchars($event['name']) . "</b>.</p>";
                        $attendeeMsg .= "<ul>";
                        $attendeeMsg .= "<li><b>Date & Time:</b> " . date('F j, Y, g:i a', strtotime($event['datetime'])) . "</li>";
                        $attendeeMsg .= "<li><b>Venue:</b> " . htmlspecialchars($event['venue']) . "</li>";
                        $attendeeMsg .= "</ul>";
                        $attendeeMsg .= "<p>Enjoy the event!<br><b>Smart Campus Events Team</b></p>";
                        
                        sendEmail($person['email'], $attendeeSubject, $attendeeMsg, true);
                    }
                }
            }
            // ----------------------------------------------
        } catch (Exception $mailEx) {
            // Silently fail if mail configuration is missing, do not break the booking flow
        }

        sendResponse(true, 'Booking successful!', ['booking_id' => $bookingId]);
    } catch (Exception $e) {
        $pdo->rollBack();
        sendResponse(false, 'Booking failed: ' . $e->getMessage());
    }
}

if ($action === 'user_list') {
    $stmt = $pdo->prepare("
        SELECT b.*, e.name as event_name, e.datetime, e.venue 
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC
    ");
    $stmt->execute([$userId]);
    sendResponse(true, 'Your bookings', $stmt->fetchAll());
}

if ($action === 'attendee_list') {
    $stmt = $pdo->prepare("
        SELECT ba.*, e.name as event_name, e.datetime, e.venue, e.id as event_id
        FROM booking_attendees ba
        JOIN bookings b ON ba.booking_id = b.id
        JOIN events e ON b.event_id = e.id
        WHERE b.user_id = ?
        ORDER BY e.datetime DESC
    ");
    $stmt->execute([$userId]);
    sendResponse(true, 'Attendee list', $stmt->fetchAll());
}

if ($action === 'admin_stats') {
    if (!$is_admin) sendResponse(false, 'Unauthorized');

    $stats = [];
    
    // Total Revenue
    $stmt = $pdo->query("SELECT SUM(total_price) as total FROM bookings");
    $stats['revenue'] = (float)($stmt->fetch()['total'] ?? 0);

    // Total Tickets
    $stmt = $pdo->query("SELECT SUM(tickets) as total FROM bookings");
    $stats['tickets'] = (int)($stmt->fetch()['total'] ?? 0);

    // Total Unique Students
    $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as total FROM bookings");
    $stats['engagement'] = (int)($stmt->fetch()['total'] ?? 0);

    // Total QR Check-ins
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM booking_attendees WHERE is_checked_in = 1");
    $stats['checkins'] = (int)($stmt->fetch()['total'] ?? 0);

    // Trending Event
    $stmt = $pdo->query("
        SELECT e.name, COUNT(*) as count 
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        GROUP BY b.event_id 
        ORDER BY count DESC 
        LIMIT 1
    ");
    $stats['trending'] = $stmt->fetch()['name'] ?? 'None';

    // Leading Department
    $stmt = $pdo->query("
        SELECT e.department, COUNT(*) as count 
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        GROUP BY e.department 
        ORDER BY count DESC 
        LIMIT 1
    ");
    $stats['top_dept'] = $stmt->fetch()['department'] ?? 'N/A';

    // Data for Revenue Chart (Top 5 Events)
    $stmt = $pdo->query("
        SELECT e.name, SUM(b.total_price) as total 
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        GROUP BY b.event_id 
        ORDER BY total DESC 
        LIMIT 5
    ");
    $stats['revenue_by_event'] = $stmt->fetchAll();

    // Data for Department Engagement
    $stmt = $pdo->query("
        SELECT e.department, COUNT(*) as count 
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        GROUP BY e.department
    ");
    $stats['dept_engagement'] = $stmt->fetchAll();

    // Data for Booking Trends (Last 7 Days)
    $stmt = $pdo->query("
        SELECT DATE(booking_date) as date, SUM(tickets) as count 
        FROM bookings 
        WHERE booking_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(booking_date)
        ORDER BY date ASC
    ");
    $stats['booking_trends'] = $stmt->fetchAll();

    sendResponse(true, 'Stats loaded', $stats);
}

if ($action === 'admin_list') {
    if (!$is_admin) sendResponse(false, 'Unauthorized');
    
    $stmt = $pdo->query("
        SELECT b.*, e.name as event_name, u.name as user_name 
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        JOIN users u ON b.user_id = u.id 
        ORDER BY b.booking_date DESC
    ");
    sendResponse(true, 'All bookings', $stmt->fetchAll());
}
?>
