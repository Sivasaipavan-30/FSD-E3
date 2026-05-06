<?php
require_once 'api/db.php';

$bookingId = $_GET['id'] ?? 0;

if (!$bookingId) {
    die("Invalid Booking ID");
}

try {
    $stmt = $pdo->prepare("
        SELECT b.*, e.name as event_name, e.datetime, e.venue, u.name as user_name, u.email as user_email
        FROM bookings b
        JOIN events e ON b.event_id = e.id
        JOIN users u ON b.user_id = u.id
        WHERE b.id = ?
    ");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();

    if (!$booking) {
        die("Booking not found");
    }

    // Fetch individual student details
    $stmt = $pdo->prepare("SELECT * FROM booking_attendees WHERE booking_id = ?");
    $stmt->execute([$bookingId]);
    $attendees = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Receipt - #<?php echo $bookingId; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; color: var(--text-main); margin: 0; padding: 40px; }
        .receipt-card { background: white; max-width: 600px; margin: 0 auto; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-top: 8px solid var(--primary); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid var(--border); padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-weight: 800; font-size: 24px; color: var(--primary); }
        .receipt-id { text-align: right; color: var(--text-muted); }
        .section-title { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 10px; font-weight: 600; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-item p { margin: 4px 0; font-weight: 500; }
        .booking-details { background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .total-row { border-top: 1px solid var(--border); padding-top: 10px; margin-top: 10px; font-size: 20px; font-weight: 700; color: var(--primary); }
        .footer { text-align: center; color: var(--text-muted); font-size: 14px; border-top: 1px solid var(--border); padding-top: 20px; }
        .no-print { margin-bottom: 20px; text-align: center; }
        .btn { padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
            .receipt-card { box-shadow: none; border: none; max-width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print">
        <button class="btn" onclick="window.print()">Print or Save as PDF</button>
        <button class="btn" style="background: #4b5563; margin-left: 10px;" onclick="window.close()">Close Window</button>
    </div>
    <div class="receipt-card">
        <div class="header">
            <div class="logo">CampusEvents</div>
            <div class="receipt-id">
                <p style="margin: 0;"><b>RECEIPT</b></p>
                <p style="margin: 0; font-size: 12px;">ID: #<?php echo str_pad($bookingId, 6, '0', STR_PAD_LEFT); ?></p>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="section-title">Customer Info</div>
                <p><?php echo htmlspecialchars($booking['user_name']); ?></p>
                <p style="color: var(--text-muted);"><?php echo htmlspecialchars($booking['user_email']); ?></p>
            </div>
            <div class="info-item" style="text-align: right;">
                <div class="section-title">Booking Date</div>
                <p><?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></p>
            </div>
        </div>

        <div class="booking-details">
            <div class="section-title">Event Details</div>
            <h2 style="margin: 10px 0;"><?php echo htmlspecialchars($booking['event_name']); ?></h2>
            <p style="margin: 5px 0;">📅 <?php echo date('F j, Y | g:i A', strtotime($booking['datetime'])); ?></p>
            <p style="margin: 5px 0;">📍 <?php echo htmlspecialchars($booking['venue']); ?></p>
        </div>

        <div style="margin-bottom: 30px;">
            <div class="detail-row">
                <span>Tickets Booked</span>
                <span><?php echo $booking['tickets']; ?></span>
            </div>
            
            <?php if (!empty($attendees)): ?>
            <div style="margin-top: 20px; border-top: 1px dashed var(--border); padding-top: 15px;">
                <div class="section-title">Registered Students</div>
                <table style="width: 100%; font-size: 13px; text-align: left; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border); color: var(--text-muted);">
                            <th style="padding-bottom: 5px;">Name / Dept</th>
                            <th style="padding-bottom: 5px;">VTU No</th>
                            <th style="padding-bottom: 5px;">Reg No</th>
                            <th style="padding-bottom: 5px; text-align: center;">Verification</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendees as $student): ?>
                        <tr style="border-bottom: 1px solid #f9fafb;">
                            <td style="padding: 10px 0;"><strong><?php echo htmlspecialchars($student['name']); ?></strong><br><small style="color:var(--text-muted)"><?php echo htmlspecialchars($student['department']); ?></small></td>
                            <td style="padding: 10px 0;"><?php echo htmlspecialchars($student['vtu_no']); ?></td>
                            <td style="padding: 10px 0;"><?php echo htmlspecialchars($student['reg_no']); ?></td>
                            <td style="padding: 10px 0; text-align: center;">
                                <?php 
                                    $currentUrl = "http://localhost/Campus_event/verify.php?id=" . $student['id'];
                                ?>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=<?php echo urlencode($currentUrl); ?>" alt="QR" style="border: 1px solid var(--border); border-radius: 4px; padding: 2px;">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <div class="total-row">
                <span>Total Paid</span>
                <span>₹<?php echo number_format($booking['total_price'], 2); ?></span>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for booking with Smart Campus Events!</p>
            <p style="font-size: 11px;">Each student must present their unique QR code for entry.</p>
        </div>
    </div>
</body>
</html>
