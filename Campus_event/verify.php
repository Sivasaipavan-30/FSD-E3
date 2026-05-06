<?php
require_once 'api/db.php';

$attendeeId = $_GET['id'] ?? 0;
$status = 'pending';
$student = null;
$message = '';

if ($attendeeId) {
    try {
        // Fetch student and event info
        $stmt = $pdo->prepare("
            SELECT ba.*, e.name as event_name, e.venue 
            FROM booking_attendees ba
            JOIN bookings b ON ba.booking_id = b.id
            JOIN events e ON b.event_id = e.id
            WHERE ba.id = ?
        ");
        $stmt->execute([$attendeeId]);
        $student = $stmt->fetch();

        if ($student) {
            if ($student['is_checked_in']) {
                $status = 'already_scanned';
                $message = "Already Checked In at " . date('g:i A', strtotime($student['checkin_time']));
            } else {
                // First time check-in - Perform update
                $stmt = $pdo->prepare("UPDATE booking_attendees SET is_checked_in = 1, checkin_time = NOW() WHERE id = ?");
                $stmt->execute([$attendeeId]);
                $status = 'success';
                $message = "Student Verified & Checked In!";
            }
        } else {
            $status = 'invalid';
            $message = "Invalid Ticket or Student Record Not Found";
        }
    } catch (PDOException $e) {
        $status = 'error';
        $message = "Internal System Error";
    }
} else {
    $status = 'invalid';
    $message = "No Attendee ID Provided";
}

// Return JSON for AJAX requests
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => in_array($status, ['success', 'already_scanned']),
        'status' => $status,
        'message' => $message,
        'data' => $student
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Entry Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg: #0f172a;
            --card: #1e293b;
            --text: #f8fafc;
        }
        body { 
            font-family: 'Outfit', sans-serif; 
            background: var(--bg); 
            color: var(--text); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            padding: 20px;
        }
        .container { 
            background: var(--card); 
            width: 100%; 
            max-width: 500px; 
            padding: 2.5rem; 
            border-radius: 20px; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .status-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 3rem;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
        .status-success { background: rgba(16, 185, 129, 0.2); color: var(--success); border: 2px solid var(--success); }
        .status-warning { background: rgba(245, 158, 11, 0.2); color: var(--warning); border: 2px solid var(--warning); }
        .status-danger { background: rgba(239, 68, 68, 0.2); color: var(--danger); border: 2px solid var(--danger); }
        
        h1 { font-size: 1.8rem; margin-bottom: 0.5rem; }
        .message { font-size: 1.1rem; color: #94a3b8; margin-bottom: 2rem; }
        
        .student-info {
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: left;
            margin-bottom: 2rem;
        }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 0.8rem; }
        .info-row:last-child { border: none; margin-bottom: 0; padding-bottom: 0; }
        .label { color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .value { font-weight: 600; color: #e2e8f0; }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: var(--text);
            color: var(--bg);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.2s;
        }
        .btn:hover { background: #cbd5e1; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($status === 'success'): ?>
            <div class="status-icon status-success">✓</div>
            <h1>Access Granted</h1>
            <p class="message"><?php echo $message; ?></p>
        <?php elseif ($status === 'already_scanned'): ?>
            <div class="status-icon status-warning">⚠</div>
            <h1>Duplicate Scan</h1>
            <p class="message"><?php echo $message; ?></p>
        <?php else: ?>
            <div class="status-icon status-danger">✕</div>
            <h1>Access Denied</h1>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if ($student): ?>
            <div class="student-info">
                <div class="info-row">
                    <span class="label">Student Name</span>
                    <span class="value"><?php echo htmlspecialchars($student['name']); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">VTU / Reg No</span>
                    <span class="value"><?php echo htmlspecialchars($student['vtu_no']); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Department</span>
                    <span class="value"><?php echo htmlspecialchars($student['department']); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Event</span>
                    <span class="value" style="color:var(--success)"><?php echo htmlspecialchars($student['event_name']); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn">Close Tab</a>
    </div>
</body>
</html>
